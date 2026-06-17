<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseOrder;
use App\Models\Lead;
use App\Models\TeamMember;
use App\Models\WebhookEvent;
use App\Models\Webinar;
use App\Models\WebinarRegistration;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BaleBotService
{
    public function __construct(private readonly BaleClient $client)
    {
    }

    public function handleUpdate(array $update): void
    {
        WebhookEvent::create([
            'event_id' => (string) Arr::get($update, 'update_id', Str::uuid()),
            'payload' => $update,
        ]);

        $message = $update['message'] ?? $update['callback_query']['message'] ?? null;
        $from = $update['message']['from'] ?? $update['callback_query']['from'] ?? [];
        $text = trim((string) ($update['message']['text'] ?? $update['callback_query']['data'] ?? ''));
        $chatId = Arr::get($message, 'chat.id');

        if (! $chatId || ! $message) {
            return;
        }

        $message['from'] = $from;

        match (true) {
            $text === '/start' || $text === 'شروع' => $this->sendWelcome($chatId),
            $text === 'لیست دوره‌ها' || $text === 'خرید دوره' => $this->courses($chatId, $message),
            $text === 'course:list' => $this->courses($chatId, $message),
            str_starts_with($text, 'course:') => $this->courseDetails($chatId, (int) Str::after($text, 'course:')),
            str_starts_with($text, 'buy:') => $this->buyCourse($chatId, $message, (int) Str::after($text, 'buy:')),
            $text === 'ثبت نام در وبینار' || $text === 'ثبت‌نام در وبینار' => $this->webinar($chatId, $message),
            str_starts_with($text, 'webinar:') => $this->registerWebinar($chatId, $message, (int) Str::after($text, 'webinar:')),
            $text === 'معرفی اعضای تیم' => $this->team($chatId),
            $text === 'پشتیبانی فنی' => $this->support($chatId, $message),
            $text === 'ارتباط با مشاورین' || $text === 'مشاوره' => $this->consultant($chatId, $message),
            default => $this->sendWelcome($chatId),
        };
    }

    private function sendWelcome(int|string $chatId): void
    {
        $this->client->sendMessage(
            $chatId,
            "سلام، به بات فروش دوره آموزشی خوش آمدید.\nیکی از گزینه‌ها را انتخاب کنید:",
            $this->mainKeyboard()
        );
    }

    private function courses(int|string $chatId, array $message): void
    {
        $this->storeLead($message, 'course_list');

        $courses = Course::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($courses->isEmpty()) {
            $this->client->sendMessage(
                $chatId,
                "فعلا دوره فعالی ثبت نشده است.\nاز پنل ادمین مسیر Courses یک دوره بسازید.",
                $this->mainKeyboard()
            );

            return;
        }

        $buttons = $courses
            ->map(fn (Course $course): array => [[
                'text' => $course->title,
                'callback_data' => 'course:'.$course->id,
            ]])
            ->values()
            ->all();

        $this->client->sendMessage(
            $chatId,
            "دوره‌های فعال:\nبرای دیدن جزئیات، روی نام دوره بزنید.",
            ['inline_keyboard' => $buttons]
        );
    }

    private function courseDetails(int|string $chatId, int $courseId): void
    {
        $course = Course::query()->where('is_active', true)->find($courseId);

        if (! $course) {
            $this->client->sendMessage($chatId, 'این دوره پیدا نشد یا غیرفعال است.', $this->mainKeyboard());

            return;
        }

        $price = $course->price > 0 ? number_format($course->price).' تومان' : 'رایگان';
        $startsAt = $course->starts_at ? $course->starts_at->format('Y-m-d H:i') : 'به‌زودی';

        $text = "عنوان دوره: {$course->title}\n"
            ."قیمت: {$price}\n"
            ."سطح: ".($course->level ?: '-')."\n"
            ."مدت زمان: ".($course->duration ?: '-')."\n"
            ."شروع: {$startsAt}\n\n"
            .($course->summary ?: $course->description ?: 'توضیحات تکمیلی هنوز ثبت نشده است.');

        $buttons = [
            [[
                'text' => 'ثبت سفارش این دوره',
                'callback_data' => 'buy:'.$course->id,
            ]],
            [[
                'text' => 'بازگشت به لیست دوره‌ها',
                'callback_data' => 'course:list',
            ]],
        ];

        $this->client->sendMessage($chatId, $text, ['inline_keyboard' => $buttons]);
    }

    private function buyCourse(int|string $chatId, array $message, int $courseId): void
    {
        $course = Course::query()->where('is_active', true)->find($courseId);

        if (! $course) {
            $this->client->sendMessage($chatId, 'این دوره برای خرید فعال نیست.', $this->mainKeyboard());

            return;
        }

        $lead = $this->storeLead($message, 'course_purchase');

        $order = CourseOrder::create([
            'course_id' => $course->id,
            'lead_id' => $lead->id,
            'amount' => $course->price,
            'status' => 'pending',
            'notes' => 'Created from Bale bot.',
        ]);

        $checkoutUrl = $course->checkout_url ?: config('bale.course_checkout_url');
        $text = "سفارش شما ثبت شد.\n"
            ."شماره سفارش: {$order->id}\n"
            ."دوره: {$course->title}\n"
            ."مبلغ: ".number_format($course->price)." تومان\n\n"
            ."برای پرداخت یا هماهنگی خرید از لینک زیر استفاده کنید:\n{$checkoutUrl}";

        $this->client->sendMessage($chatId, $text, $this->mainKeyboard());
    }

    private function webinar(int|string $chatId, array $message): void
    {
        $this->storeLead($message, 'webinar');

        $webinars = Webinar::query()
            ->where('is_active', true)
            ->orderBy('starts_at')
            ->get();

        if ($webinars->isEmpty()) {
            $this->client->sendMessage(
                $chatId,
                "فعلا وبینار فعالی ثبت نشده است.\nاز پنل ادمین مسیر Webinars یک وبینار بسازید.",
                $this->mainKeyboard()
            );

            return;
        }

        $buttons = $webinars
            ->map(fn (Webinar $webinar): array => [[
                'text' => $webinar->title,
                'callback_data' => 'webinar:'.$webinar->id,
            ]])
            ->values()
            ->all();

        $this->client->sendMessage($chatId, 'وبینارهای فعال:', ['inline_keyboard' => $buttons]);
    }

    private function registerWebinar(int|string $chatId, array $message, int $webinarId): void
    {
        $webinar = Webinar::query()->where('is_active', true)->find($webinarId);

        if (! $webinar) {
            $this->client->sendMessage($chatId, 'این وبینار پیدا نشد یا غیرفعال است.', $this->mainKeyboard());

            return;
        }

        $lead = $this->storeLead($message, 'webinar_registration');

        WebinarRegistration::updateOrCreate(
            [
                'webinar_id' => $webinar->id,
                'lead_id' => $lead->id,
            ],
            ['status' => 'registered']
        );

        $startsAt = $webinar->starts_at ? $webinar->starts_at->format('Y-m-d H:i') : 'به‌زودی';
        $registrationUrl = $webinar->registration_url ?: config('bale.webinar_landing_url');

        $this->client->sendMessage(
            $chatId,
            "ثبت‌نام وبینار انجام شد.\n"
            ."عنوان: {$webinar->title}\n"
            ."زمان: {$startsAt}\n\n"
            ."لینک تکمیل ثبت‌نام/اطلاعات بیشتر:\n{$registrationUrl}",
            $this->mainKeyboard()
        );
    }

    private function team(int|string $chatId): void
    {
        $members = TeamMember::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($members->isEmpty()) {
            $this->client->sendMessage(
                $chatId,
                "هنوز عضوی برای تیم ثبت نشده است.\nاز پنل ادمین مسیر Team members اعضا را اضافه کنید.",
                $this->mainKeyboard()
            );

            return;
        }

        $text = "اعضای تیم دوره:\n\n".$members
            ->map(fn (TeamMember $member): string => "{$member->name}\n{$member->role}\n".($member->bio ?: ''))
            ->implode("\n\n");

        $this->client->sendMessage($chatId, $text, $this->mainKeyboard());
    }

    private function support(int|string $chatId, array $message): void
    {
        $this->storeLead($message, 'technical_support');

        $this->client->sendMessage(
            $chatId,
            "برای پشتیبانی فنی از این مسیر پیام بدهید:\n".config('bale.support_technical_url'),
            $this->mainKeyboard()
        );
    }

    private function consultant(int|string $chatId, array $message): void
    {
        $this->storeLead($message, 'consultant');

        $this->client->sendMessage(
            $chatId,
            "برای ارتباط با مشاورین دوره از این مسیر استفاده کنید:\n".config('bale.support_consultant_url'),
            $this->mainKeyboard()
        );
    }

    private function storeLead(array $message, string $intent): Lead
    {
        $from = $message['from'] ?? [];
        $chat = $message['chat'] ?? [];
        $baleUserId = (string) ($from['id'] ?? $chat['id']);

        return Lead::updateOrCreate(
            [
                'bale_user_id' => $baleUserId,
                'intent' => $intent,
            ],
            [
                'chat_id' => (string) ($chat['id'] ?? null),
                'first_name' => $from['first_name'] ?? null,
                'last_name' => $from['last_name'] ?? null,
                'username' => $from['username'] ?? null,
                'last_message' => $message['text'] ?? null,
                'metadata' => $message,
            ]
        );
    }

    private function mainKeyboard(): array
    {
        return [
            'keyboard' => [
                [['text' => 'لیست دوره‌ها']],
                [['text' => 'ثبت نام در وبینار'], ['text' => 'معرفی اعضای تیم']],
                [['text' => 'پشتیبانی فنی'], ['text' => 'ارتباط با مشاورین']],
            ],
            'resize_keyboard' => true,
        ];
    }
}
