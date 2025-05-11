<?php

namespace DreamTeam\Page\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use DreamTeam\Page\Models\Page;

class PageTest extends TestCase
{

    /**
     * Feature get index page.
     *
     * @return void
     */
    public function testGetIndex()
    {
        // lấy user admin
        $admin = \DreamTeam\AdminUser\Models\AdminUser::find(1);

        // Gán hành động với guard 'admin' và gửi request
        $response = $this->actingAs($admin, 'admin')
                         ->get('/admin/pages');
        $response->assertStatus(200)
        	->assertViewIs('Table::index');
    }

    public function testCreatePage()
    {
        $admin = \DreamTeam\AdminUser\Models\AdminUser::find(1);
    	$response = $this->actingAs($admin, 'admin')->get(route('admin.pages.create'));
        $response->assertStatus(200);
        $response->assertViewIs('Form::create');
    }

    public function testStorePageSuccess()
    {
        $admin = \DreamTeam\AdminUser\Models\AdminUser::find(1);
        $testData = [
        	'name' => 'Pages name',
        	'slug' => 'page-name',
        	'detail' => 'Page detail'
        ];
    	$response = $this->actingAs($admin, 'admin')
    		->post(route('admin.pages.store'), $testData + ['redirect' => 'save']);
    	// Lấy bản ghi vừa tạo
        $page = Page::where($testData)->orderBy('id', 'DESC')->first();
        $response->assertStatus(302) // test status code 302
                ->assertRedirect('/admin/pages/' . $page->id . '/edit') // test redirect
                ->assertSessionHas('message', 'Thêm mới thành công.'); // test session
    }

}