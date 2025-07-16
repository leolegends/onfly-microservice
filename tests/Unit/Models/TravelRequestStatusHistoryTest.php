<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\TravelRequest;
use App\Models\TravelRequestStatusHistory;
use Tests\TestCase;
use Tests\Traits\TestHelpers;

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
            'travel_request_id' => 1,
            'status' => 'approved',
            'comment' => 'Aprovado pela administração',
            'changed_by' => 1,
        ];

        $statusHistory->fill($data);

        $this->assertEquals(1, $statusHistory->travel_request_id);
        $this->assertEquals('approved', $statusHistory->status);
        $this->assertEquals('Aprovado pela administração', $statusHistory->comment);
        $this->assertEquals(1, $statusHistory->changed_by);
    }

    /** @test */
    public function status_history_can_be_created_with_factory()
    {
        $user = $this->createUser();
        $travelRequest = $this->createTravelRequest(['user_id' => $user->id]);
        
        $statusHistory = TravelRequestStatusHistory::factory()->create([
            'travel_request_id' => $travelRequest->id,
            'status' => 'approved',
            'comment' => 'Teste de aprovação',
            'changed_by' => $user->id,
        ]);

        $this->assertEquals($travelRequest->id, $statusHistory->travel_request_id);
        $this->assertEquals('approved', $statusHistory->status);
        $this->assertEquals('Teste de aprovação', $statusHistory->comment);
        $this->assertEquals($user->id, $statusHistory->changed_by);
    }

    /** @test */
    public function status_history_is_created_automatically_when_travel_request_is_created()
    {
        $user = $this->createUser();
        $travelRequest = $this->createTravelRequest(['user_id' => $user->id]);

        $this->assertCount(1, $travelRequest->statusHistory);
        $statusHistory = $travelRequest->statusHistory->first();
        $this->assertEquals(TravelRequest::STATUS_REQUESTED, $statusHistory->status);
    }

    /** @test */
    public function status_history_is_created_automatically_when_travel_request_status_changes()
    {
        $user = $this->createUser();
        $travelRequest = $this->createTravelRequest(['user_id' => $user->id]);
        
        // Verificar que há 1 registro inicial
        $this->assertCount(1, $travelRequest->statusHistory);

        // Alterar status
        $travelRequest->update(['status' => TravelRequest::STATUS_APPROVED]);
        $travelRequest->refresh();

        // Verificar que há 2 registros agora
        $this->assertCount(2, $travelRequest->statusHistory);
        
        $latestHistory = $travelRequest->statusHistory->sortBy('created_at')->last();
        $this->assertEquals(TravelRequest::STATUS_APPROVED, $latestHistory->status);
    }

    /** @test */
    public function status_history_can_get_formatted_date()
    {
        $user = $this->createUser();
        $travelRequest = $this->createTravelRequest(['user_id' => $user->id]);
        $statusHistory = $travelRequest->statusHistory->first();

        $formattedDate = $statusHistory->getFormattedDate();
        
        $this->assertIsString($formattedDate);
        $this->assertMatchesRegularExpression('/\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}/', $formattedDate);
    }

    /** @test */
    public function status_history_can_get_status_label()
    {
        $user = $this->createUser();
        $travelRequest = $this->createTravelRequest(['user_id' => $user->id]);
        
        $statusHistory = TravelRequestStatusHistory::factory()->create([
            'travel_request_id' => $travelRequest->id,
            'status' => TravelRequest::STATUS_APPROVED,
        ]);

        $this->assertEquals('Aprovado', $statusHistory->getStatusLabel());
    }

    /** @test */
    public function status_history_orders_by_created_at_desc_by_default()
    {
        $user = $this->createUser();
        $travelRequest = $this->createTravelRequest(['user_id' => $user->id]);
        
        // Criar mais registros de histórico
        TravelRequestStatusHistory::factory()->create([
            'travel_request_id' => $travelRequest->id,
            'status' => TravelRequest::STATUS_APPROVED,
            'created_at' => now()->subMinutes(30),
        ]);
        
        TravelRequestStatusHistory::factory()->create([
            'travel_request_id' => $travelRequest->id,
            'status' => TravelRequest::STATUS_REJECTED,
            'created_at' => now()->subMinutes(10),
        ]);

        $statusHistory = TravelRequestStatusHistory::where('travel_request_id', $travelRequest->id)->get();
        
        // Verificar que estão ordenados por data de criação (mais recente primeiro)
        $this->assertEquals(TravelRequest::STATUS_REJECTED, $statusHistory->first()->status);
    }

    /** @test */
    public function status_history_can_filter_by_status()
    {
        $user = $this->createUser();
        $travelRequest = $this->createTravelRequest(['user_id' => $user->id]);
        
        TravelRequestStatusHistory::factory()->create([
            'travel_request_id' => $travelRequest->id,
            'status' => TravelRequest::STATUS_APPROVED,
        ]);
        
        TravelRequestStatusHistory::factory()->create([
            'travel_request_id' => $travelRequest->id,
            'status' => TravelRequest::STATUS_REJECTED,
        ]);

        $approvedHistory = TravelRequestStatusHistory::where('status', TravelRequest::STATUS_APPROVED)->get();
        $rejectedHistory = TravelRequestStatusHistory::where('status', TravelRequest::STATUS_REJECTED)->get();

        $this->assertCount(1, $approvedHistory);
        $this->assertCount(1, $rejectedHistory);
        $this->assertEquals(TravelRequest::STATUS_APPROVED, $approvedHistory->first()->status);
        $this->assertEquals(TravelRequest::STATUS_REJECTED, $rejectedHistory->first()->status);
    }

    /** @test */
    public function status_history_can_include_comment()
    {
        $user = $this->createUser();
        $travelRequest = $this->createTravelRequest(['user_id' => $user->id]);
        
        $statusHistory = TravelRequestStatusHistory::factory()->create([
            'travel_request_id' => $travelRequest->id,
            'status' => TravelRequest::STATUS_APPROVED,
            'comment' => 'Aprovado após análise detalhada',
        ]);

        $this->assertEquals('Aprovado após análise detalhada', $statusHistory->comment);
    }

    /** @test */
    public function status_history_can_track_who_changed_status()
    {
        $user = $this->createUser();
        $admin = $this->createAdmin();
        $travelRequest = $this->createTravelRequest(['user_id' => $user->id]);
        
        $statusHistory = TravelRequestStatusHistory::factory()->create([
            'travel_request_id' => $travelRequest->id,
            'status' => TravelRequest::STATUS_APPROVED,
            'changed_by' => $admin->id,
        ]);

        $this->assertEquals($admin->id, $statusHistory->changed_by);
    }
}
