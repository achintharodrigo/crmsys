<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Passport::actingAs($this->user);
    }

    /**
     * @test
     */
    public function if_user_can_list_all_customers_in_the_customer_table() : void
    {
        Customer::factory(10)->create();

        $response = $this->get(route('customers.data'))->assertStatus(200);

        $this->assertDatabaseCount('customers', 10);
    }

    /**
     * @test
     */
    public function if_user_can_create_a_customer() : void
    {
        $response = $this->post(route('customer.store'), [
            'first_name' => 'test_first_name',
            'last_name' => 'test_last_name',
            'email' => 'email@emil.com',
            'mobile_number' => '+11111111111',
        ])->assertStatus(200);

        $this->assertDatabaseCount('customers', 1);
    }

    /**
     * @test
     */
    public function if_user_can_update_a_customer() : void
    {
        Customer::factory()->create([
            'first_name' => 'first_name_1',
            'last_name' => 'last_name_1',
            'email' => 'email1@email.com',
            'mobile_number' => '+11111111111'
        ]);

        $this->assertDatabaseCount('customers', 1);
        $customer = Customer::first();

        $response = $this->put(route('customer.update', [$customer->id]), [
            'first_name' => 'first_name_2',
            'last_name' => 'first_name_2',
            'email' => 'email2@emil.com',
            'mobile_number' => '+22222222222',
        ])->assertStatus(200);

        $customer = Customer::first();

        $this->assertDatabaseCount('customers', 1);
        $this->assertEquals("first_name_2", $customer->first_name);
    }


    /**
     * @test
     */
    public function if_user_can_delete_a_customer() : void
    {
        Customer::factory()->create();

        $this->assertDatabaseCount('customers', 1);

        $customer = Customer::first();

        $response = $this->delete(route('customer.destroy', $customer->id))
            ->assertStatus(200);

        $this->assertDatabaseCount('customers', 0);
    }
}
