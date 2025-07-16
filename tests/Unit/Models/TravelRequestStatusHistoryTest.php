<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\TravelRequest;
use Tests\Traits\TestHelpers;
use Illuminate\Support\Carbon;
use App\Models\TravelRequestStatusHistory;

class TravelRequestStatusHistoryTest extends TestCase
{
    use TestHelpers;

    /** @test */
    public function status_history_belongs_to_travel_request()
    {
        $user = $this->createUser();
        $travelRequest = $this->createTravelRequest(['user_id' => $user->id]);
        $statusHistory = $travelRequest->statusHistory->first();

        $this->assertInstanceOf(TravelRequest::class, $statusHistory->travelRequest);
        $this->assertEquals($travelRequest->id, $statusHistory->travelRequest->id);
    }

    /** @test */
    public function status_history_has_fillable_attributes()
    {
        $statusHistory = new TravelRequestStatusHistory();
        $data = [
            'user_id' => 1,
            'travel_request_id' => 1,
            'status' => 'approved',
            'comment' => 'Aprovado pela administração',
            'changed_by' => 1,
        ];

        $statusHistory->fill($data);

        $this->assertEquals(1, $statusHistory->user_id);
        $this->assertEquals(1, $statusHistory->travel_request_id);
    }

    /** @test */
    public function status_history_can_be_created_with_factory()
    {
        $user = $this->createUser();

        $payload = [
            'user_id' => $user->id,
            'destination' => 'São Paulo',
            'departure_date' => Carbon::now()->addDays(1)->format('d-m-Y'),
            'return_date' => Carbon::now()->addDays(5)->format('d-m-Y'),
            'purpose' => 'Reunião com cliente',
            'budget' => 1500.00,
        ];

        $travelRequest = $this->createTravelRequest($payload);
        
        $statusHistory = TravelRequestStatusHistory::factory()->create([
            'user_id' => $user->id,
            'travel_request_id' => $travelRequest->id,
            'status' => 'requested',
            'comment' => 'Teste de aprovação',
            'changed_by' => $user->id,
            'new_status' => 'approved',
        ]);

        $this->assertEquals($travelRequest->id, $statusHistory->travel_request_id);
        $this->assertEquals('requested', $statusHistory->status);
        $this->assertEquals('Teste de aprovação', $statusHistory->comment);
        $this->assertEquals($user->id, $statusHistory->changed_by);
    }

    /** @test */
    public function status_history_can_filter_by_status()
    {
        $user = $this->createUser();
        
        $payload = [
            'user_id' => $user->id,
            'destination' => 'São Paulo',
            'departure_date' => Carbon::now()->addDays(1)->format('d-m-Y'),
            'return_date' => Carbon::now()->addDays(5)->format('d-m-Y'),
            'purpose' => 'Reunião com cliente',
            'budget' => 1500.00,
        ];

        $travelRequest = $this->createTravelRequest($payload);
        
        TravelRequestStatusHistory::factory()->create([
            'user_id' => $user->id,
            'travel_request_id' => $travelRequest->id,
            'status' => TravelRequest::STATUS_APPROVED,
            'new_status' => TravelRequest::STATUS_APPROVED,
        ]);
        
        TravelRequestStatusHistory::factory()->create([
            'user_id' => $user->id,
            'travel_request_id' => $travelRequest->id,
            'status' => TravelRequest::STATUS_REJECTED,
            'new_status' => TravelRequest::STATUS_REJECTED,
            
        ]);

        $approvedHistory = TravelRequestStatusHistory::where('status', TravelRequest::STATUS_APPROVED)->get();
        $rejectedHistory = TravelRequestStatusHistory::where('status', TravelRequest::STATUS_REJECTED)->get();

        $this->assertCount(1, $approvedHistory);
        $this->assertCount(1, $rejectedHistory);
        $this->assertEquals(TravelRequest::STATUS_APPROVED, $approvedHistory->first()->status);
        $this->assertEquals(TravelRequest::STATUS_REJECTED, $rejectedHistory->first()->status);
    }


}
