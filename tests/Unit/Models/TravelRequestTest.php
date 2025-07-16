<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\TravelRequest;
use App\Models\TravelRequestStatusHistory;
use Carbon\Carbon;
use Tests\TestCase;
use Tests\Traits\TestHelpers;

class TravelRequestTest extends TestCase
{
    use TestHelpers;

    /** @test */
    public function travel_request_belongs_to_user()
    {
        $user = $this->createUser();
        $travelRequest = $this->createTravelRequest(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $travelRequest->user);
        $this->assertEquals($user->id, $travelRequest->user->id);
    }

    /** @test */
    public function travel_request_has_status_history()
    {
        $user = $this->createUser();
        $travelRequest = $this->createTravelRequest(['user_id' => $user->id]);

        // O Observer deve criar automaticamente o histórico
        $this->assertInstanceOf(TravelRequestStatusHistory::class, $travelRequest->statusHistory->first());
    }

    /** @test */
    public function travel_request_has_fillable_attributes()
    {
        $travelRequest = new TravelRequest();
        $data = [
            'user_id' => 1,
            'destination' => 'São Paulo',
            'start_date' => '2024-02-01',
            'end_date' => '2024-02-05',
            'purpose' => 'Reunião com cliente',
            'status' => 'requested',
            'budget' => 1500.00,
            'notes' => 'Algumas observações',
        ];

        $travelRequest->fill($data);

        $this->assertEquals(1, $travelRequest->user_id);
        $this->assertEquals('São Paulo', $travelRequest->destination);
        $this->assertEquals('2024-02-01', $travelRequest->start_date);
        $this->assertEquals('2024-02-05', $travelRequest->end_date);
        $this->assertEquals('Reunião com cliente', $travelRequest->purpose);
        $this->assertEquals('requested', $travelRequest->status);
        $this->assertEquals(1500.00, $travelRequest->budget);
        $this->assertEquals('Algumas observações', $travelRequest->notes);
    }

    /** @test */
    public function travel_request_has_date_casts()
    {
        $travelRequest = new TravelRequest();
        $casts = $travelRequest->getCasts();

        $this->assertArrayHasKey('start_date', $casts);
        $this->assertArrayHasKey('end_date', $casts);
        $this->assertEquals('date', $casts['start_date']);
        $this->assertEquals('date', $casts['end_date']);
    }

    /** @test */
    public function travel_request_has_budget_cast()
    {
        $travelRequest = new TravelRequest();
        $casts = $travelRequest->getCasts();

        $this->assertArrayHasKey('budget', $casts);
        $this->assertEquals('decimal:2', $casts['budget']);
    }

    /** @test */
    public function travel_request_has_default_status()
    {
        $travelRequest = new TravelRequest();
        
        $this->assertEquals(TravelRequest::STATUS_REQUESTED, $travelRequest->status);
    }

    /** @test */
    public function travel_request_can_check_if_is_requested()
    {
        $travelRequest = $this->createTravelRequest(['status' => TravelRequest::STATUS_REQUESTED]);
        
        $this->assertTrue($travelRequest->isRequested());
        $this->assertFalse($travelRequest->isApproved());
        $this->assertFalse($travelRequest->isRejected());
        $this->assertFalse($travelRequest->isCancelled());
    }

    /** @test */
    public function travel_request_can_check_if_is_approved()
    {
        $travelRequest = $this->createTravelRequest(['status' => TravelRequest::STATUS_APPROVED]);
        
        $this->assertFalse($travelRequest->isRequested());
        $this->assertTrue($travelRequest->isApproved());
        $this->assertFalse($travelRequest->isRejected());
        $this->assertFalse($travelRequest->isCancelled());
    }

    /** @test */
    public function travel_request_can_check_if_is_rejected()
    {
        $travelRequest = $this->createTravelRequest(['status' => TravelRequest::STATUS_REJECTED]);
        
        $this->assertFalse($travelRequest->isRequested());
        $this->assertFalse($travelRequest->isApproved());
        $this->assertTrue($travelRequest->isRejected());
        $this->assertFalse($travelRequest->isCancelled());
    }

    /** @test */
    public function travel_request_can_check_if_is_cancelled()
    {
        $travelRequest = $this->createTravelRequest(['status' => TravelRequest::STATUS_CANCELLED]);
        
        $this->assertFalse($travelRequest->isRequested());
        $this->assertFalse($travelRequest->isApproved());
        $this->assertFalse($travelRequest->isRejected());
        $this->assertTrue($travelRequest->isCancelled());
    }

    /** @test */
    public function travel_request_can_get_duration_in_days()
    {
        $travelRequest = $this->createTravelRequest([
            'start_date' => '2024-02-01',
            'end_date' => '2024-02-05',
        ]);

        $this->assertEquals(5, $travelRequest->getDurationInDays());
    }

    /** @test */
    public function travel_request_can_check_if_is_in_past()
    {
        $pastRequest = $this->createTravelRequest([
            'start_date' => Carbon::yesterday()->toDateString(),
            'end_date' => Carbon::yesterday()->toDateString(),
        ]);

        $futureRequest = $this->createTravelRequest([
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDays(2)->toDateString(),
        ]);

        $this->assertTrue($pastRequest->isInPast());
        $this->assertFalse($futureRequest->isInPast());
    }

    /** @test */
    public function travel_request_can_check_if_is_in_future()
    {
        $pastRequest = $this->createTravelRequest([
            'start_date' => Carbon::yesterday()->toDateString(),
            'end_date' => Carbon::yesterday()->toDateString(),
        ]);

        $futureRequest = $this->createTravelRequest([
            'start_date' => Carbon::tomorrow()->toDateString(),
            'end_date' => Carbon::tomorrow()->addDays(2)->toDateString(),
        ]);

        $this->assertFalse($pastRequest->isInFuture());
        $this->assertTrue($futureRequest->isInFuture());
    }

    /** @test */
    public function travel_request_can_check_if_is_current()
    {
        $currentRequest = $this->createTravelRequest([
            'start_date' => Carbon::yesterday()->toDateString(),
            'end_date' => Carbon::tomorrow()->toDateString(),
        ]);

        $pastRequest = $this->createTravelRequest([
            'start_date' => Carbon::yesterday()->subDays(5)->toDateString(),
            'end_date' => Carbon::yesterday()->toDateString(),
        ]);

        $this->assertTrue($currentRequest->isCurrent());
        $this->assertFalse($pastRequest->isCurrent());
    }

    /** @test */
    public function travel_request_can_get_formatted_budget()
    {
        $travelRequest = $this->createTravelRequest(['budget' => 1500.50]);

        $this->assertEquals('R$ 1.500,50', $travelRequest->getFormattedBudget());
    }

    /** @test */
    public function travel_request_can_get_formatted_dates()
    {
        $travelRequest = $this->createTravelRequest([
            'start_date' => '2024-02-01',
            'end_date' => '2024-02-05',
        ]);

        $this->assertEquals('01/02/2024', $travelRequest->getFormattedStartDate());
        $this->assertEquals('05/02/2024', $travelRequest->getFormattedEndDate());
    }

    /** @test */
    public function travel_request_can_get_status_label()
    {
        $requested = $this->createTravelRequest(['status' => TravelRequest::STATUS_REQUESTED]);
        $approved = $this->createTravelRequest(['status' => TravelRequest::STATUS_APPROVED]);
        $rejected = $this->createTravelRequest(['status' => TravelRequest::STATUS_REJECTED]);
        $cancelled = $this->createTravelRequest(['status' => TravelRequest::STATUS_CANCELLED]);

        $this->assertEquals('Solicitado', $requested->getStatusLabel());
        $this->assertEquals('Aprovado', $approved->getStatusLabel());
        $this->assertEquals('Rejeitado', $rejected->getStatusLabel());
        $this->assertEquals('Cancelado', $cancelled->getStatusLabel());
    }
}
