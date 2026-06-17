<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseOrder;
use App\Models\Lead;
use App\Models\Webinar;
use App\Models\WebinarRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_order_belongs_to_course_and_lead(): void
    {
        $course = Course::create(['title' => 'Course', 'slug' => 'course']);
        $lead = Lead::create(['bale_user_id' => '42', 'chat_id' => '42', 'intent' => 'course_purchase']);

        $order = CourseOrder::create([
            'course_id' => $course->id,
            'lead_id' => $lead->id,
            'amount' => 1000,
            'status' => 'pending',
        ]);

        $this->assertTrue($order->course->is($course));
        $this->assertTrue($order->lead->is($lead));
        $this->assertTrue($course->orders->first()->is($order));
        $this->assertTrue($lead->courseOrders->first()->is($order));
    }

    public function test_webinar_registration_belongs_to_webinar_and_lead(): void
    {
        $webinar = Webinar::create(['title' => 'Webinar']);
        $lead = Lead::create(['bale_user_id' => '42', 'chat_id' => '42', 'intent' => 'webinar_registration']);

        $registration = WebinarRegistration::create([
            'webinar_id' => $webinar->id,
            'lead_id' => $lead->id,
            'status' => 'registered',
        ]);

        $this->assertTrue($registration->webinar->is($webinar));
        $this->assertTrue($registration->lead->is($lead));
        $this->assertTrue($webinar->registrations->first()->is($registration));
        $this->assertTrue($lead->webinarRegistrations->first()->is($registration));
    }

    public function test_json_and_boolean_casts_are_applied(): void
    {
        $lead = Lead::create([
            'bale_user_id' => '42',
            'chat_id' => '42',
            'intent' => 'course_list',
            'metadata' => ['message' => ['text' => 'course:list']],
        ]);
        $course = Course::create([
            'title' => 'Course',
            'slug' => 'course',
            'price' => '1000',
            'is_active' => 1,
        ]);

        $this->assertSame('course:list', $lead->metadata['message']['text']);
        $this->assertSame(1000, $course->price);
        $this->assertTrue($course->is_active);
    }
}
