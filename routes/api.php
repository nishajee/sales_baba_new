<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->group(function () {
// Route::get('leads','Api_Controller\LeadsController@leads');
Route::get('getUserDetail','Api_Controller\LeadsController@getUserDetail');
});
Route::post('call_response/add_call_response','Api_Controller\MasterController@add_call_response');

Route::get('leads','Api_Controller\LeadsController@leads');
Route::get('all_leads','Api_Controller\LeadsController@all_leads');
Route::get('leads/{id}','Api_Controller\LeadsController@leadById');
Route::post('leads','Api_Controller\LeadsController@leadSave');
Route::put('leads/{lead}','Api_Controller\LeadsController@leadUpdate');
Route::delete('leads/{lead}','Api_Controller\LeadsController@leadDelete');
Route::get('leadsByStatus','Api_Controller\LeadsController@searchByLeadStatus');
Route::post('leadsByOwner','Api_Controller\LeadsController@searchByLeadOwner');
Route::post('leads/AssignLeadToSalePerson','Api_Controller\LeadsController@AssignLeadToSalePerson');
Route::post('leads/AssignContactsToSalePerson','Api_Controller\LeadsController@AssignContactsToSalePerson');
Route::post('leads/updateContactToLead','Api_Controller\LeadsController@updateContactToLead');
Route::post('leads/addAllLeadActivity','Api_Controller\LeadsController@addAllLeadActivity');
Route::post('leads/addLeadsReminder','Api_Controller\LeadsController@addLeadsReminder');
Route::post('leads/FetechLeadActivity','Api_Controller\LeadsController@FetechLeadActivity');
Route::get('leads/Fetch/TelecallerDataByOwnerId','Api_Controller\leadsController@FetchTelecallerDataByOwnerId');
Route::post('leads/importleads','Api_Controller\LeadsController@importleads');
Route::get('leads/notification/view','Api_Controller\LeadsController@NotificationReminder');

Route::post('all_leads','Api_Controller\LeadsController@all_leads');
Route::post('contacts/list','Api_Controller\LeadsController@getAllContactsList');

Route::post('dashboard/GetAllDashboarData','Api_Controller\LeadsController@GetAllDashboarData');
Route::post('users/updateUserDetails/{user_id}','Api_Controller\UsersController@updateUserDetails');

Route::get('contacts/list','Api_Controller\LeadsController@getAllContactsList');
Route::get('calls','Api_Controller\Call_leadController@Leadcalls');
Route::get('calls/{id}','Api_Controller\Call_leadController@CallById');
Route::post('calls','Api_Controller\Call_leadController@CallSave');
Route::put('calls/{call}','Api_Controller\Call_leadController@CallUpdate');
Route::delete('calls/{call}','Api_Controller\Call_leadController@CallDelete');

Route::get('tasks','Api_Controller\TaskController@LeadTask');
Route::get('tasks/{id}','Api_Controller\TaskController@TaskById');
Route::post('tasks','Api_Controller\TaskController@TaskSave');
Route::put('tasks/{task}','Api_Controller\TaskController@TaskUpdate');
Route::delete('tasks/{task}','Api_Controller\TaskController@TaskDelete');

Route::post('users/fetch_team_member_by_team_head','Api_Controller\UsersController@fetch_team_member_by_team_head');
Route::post('users/fetch_team_member_for_telecalling_assign','Api_Controller\UsersController@fetch_team_member_for_telecalling_assign');
Route::get('sales_people','Api_Controller\UsersController@users');
Route::get('users/list_sale_people_in_for_assign','Api_Controller\UsersController@list_sales_people');
Route::get('sales_people/fetch/{id}','Api_Controller\UsersController@usersById');
Route::get('userProfile/fetch/{id}','Api_Controller\UsersController@usersById');
Route::post('sales_people/add','Api_Controller\UsersController@usersSave');
Route::post('sales_people/add/{user_id}','Api_Controller\UsersController@usersSave');
Route::put('users/{user}','Api_Controller\UsersController@usersUpdate');
Route::delete('users/{user}','Api_Controller\UsersController@usersDelete');

Route::get('events','Api_Controller\EventController@events');
Route::get('events/{id}','Api_Controller\EventController@eventById');
Route::post('events','Api_Controller\EventController@eventSave');
Route::put('events/{user}','Api_Controller\EventController@eventUpdate');
Route::delete('events/{user}','Api_Controller\EventController@eventDelete');

Route::get('email','Api_Controller\EmialController@emails');
Route::get('email/{id}','Api_Controller\EmialController@emailById');
Route::post('email','Api_Controller\EmialController@emailSave');
Route::put('email/{email}','Api_Controller\EmialController@emaillUpdate');
Route::delete('email/{email}','Api_Controller\EmialController@emailDelete');

Route::get('reminders','Api_Controller\ReminderController@reminders');
Route::get('reminders/{id}','Api_Controller\ReminderController@reminderById');
Route::post('reminders','Api_Controller\ReminderController@reminderSave');
Route::put('reminders/{reminder}','Api_Controller\ReminderController@reminderUpdate');
Route::delete('reminders/{reminder}','Api_Controller\ReminderController@reminderDelete');


Route::get('notes','Api_Controller\NoteController@notes');
Route::get('notes/{id}','Api_Controller\NoteController@noteById');
Route::post('notes','Api_Controller\NoteController@noteSave');
Route::put('notes/{note}','Api_Controller\NoteController@noteUpdate');
Route::delete('notes/{note}','Api_Controller\NoteController@noteDelete');

Route::get('attachments','Api_Controller\AttachmentController@attachments');
Route::get('attachments/{id}','Api_Controller\AttachmentController@attachmentById');
Route::post('attachments','Api_Controller\AttachmentController@attachmentSave');
Route::put('attachments/{attachment}','Api_Controller\AttachmentController@attachmentUpdate');
Route::delete('attachments/{attachment}','Api_Controller\AttachmentController@attachmentDelete');

// Route::get('response','Api_Controller\ResponseController_api@response_details');
// Route::get('response/{id}','Api_Controller\ResponseController_api@responseById');
// Route::post('response','Api_Controller\ResponseController_api@responseSave');
// Route::put('response/{resp}','Api_Controller\ResponseController_api@responseUpdate');
// Route::delete('response/{resp}','Api_Controller\ResponseController_api@responseDelete');

// Route::get('Busyresponse','Api_Controller\BusyResponseController@Busyresponse_details');
// Route::get('Busyresponse/{id}','Api_Controller\BusyResponseController@BusyresponseById');
// Route::post('Busyresponse','Api_Controller\BusyResponseController@BusyresponseSave');
// Route::put('Busyresponse/{bresp}','Api_Controller\BusyResponseController@BusyresponseUpdate');
// Route::delete('Busyresponse/{bresp}','Api_Controller\BusyResponseController@BusyresponseDelete');
//Masters
Route::get('call_response/list_call_response','Api_Controller\MasterController@list_call_response');
Route::get('call_response/fetch_call_response/{id}','Api_Controller\MasterController@fetch_call_response');
Route::get('call_response/delete_call_response/{id}','Api_Controller\MasterController@delete_call_response');
Route::get('LeadStatus/listAllLeadStatus','Api_Controller\MasterController@listAllLeadStatus');
Route::post('LeadStatus/addLeadStatus','Api_Controller\MasterController@addLeadStatus');
Route::get('LeadStatus/fetchLeadStatusById/{id}','Api_Controller\MasterController@fetchLeadStatusById');
Route::get('LeadStatus/DeleteLeadStatus/{id}','Api_Controller\MasterController@DeleteLeadStatus');
Route::get('call_Stage/listCallStage','Api_Controller\MasterController@listCallStage');
Route::post('call_Stage/addCallStage','Api_Controller\MasterController@addCallStage');
Route::get('call_Stage/fetchCallStage/{id}','Api_Controller\MasterController@fetchCallStage');
Route::get('call_Stage/deleteCallStage/{id}','Api_Controller\MasterController@deleteCallStage');

Route::post('designation/listDesignation','Api_Controller\MasterController@listDesignation');
Route::post('designation/addDesignation','Api_Controller\MasterController@addDesignation');
Route::get('designation/fetch/{id}','Api_Controller\MasterController@fetchDesignationById');
Route::post('listDesignationForDataMiner_Telecaller','Api_Controller\MasterController@listDesignationForDataMiner_Telecaller');

// Route::get('org','Api_Controller\MasterController@org_details');
// Route::get('orgByuser','Api_Controller\MasterController@orgById');
// Route::post('org','Api_Controller\MasterController@orgSave');
// Route::put('org/{bresp}','Api_Controller\MasterController@orgUpdate');
// Route::delete('org/{bresp}','Api_Controller\MasterController@orgDelete');
Route::get('orglist','Api_Controller\OrganizationController@list_org');
    Route::get('list_countries','Api_Controller\OrganizationController@get_country');
Route::get('list_state/{country_id}','Api_Controller\OrganizationController@getStateUsingCountry');
Route::get('list_cities/{state_id}','Api_Controller\OrganizationController@getCitiesUsingState');
Route::get('fetch_org_data/{org_id}','Api_Controller\OrganizationController@fetch_org_data');
Route::post('org/store','Api_Controller\OrganizationController@store');

// created by amit sir
Route::get('leadDelete/{lead}','Api_Controller\LeadsController@leadDelete');
//created by nisha

Route::post('users/fetch_team_head','Api_Controller\UsersController@fetch_team_head');
Route::post('users/fetch_team_head_delete','Api_Controller\UsersController@fetch_team_head_delete');
Route::post('users/advance_search_for_admin','Api_Controller\UsersController@advance_search_for_admin');
//created by sanjeet
Route::post('contacts/importcontacts','Api_Controller\ContactController@importcontacts');




Route::group([
    'middleware' => 'api',
    // 'prefix' => 'auth'
], function ($router) {
    Route::post('/user/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');
    Route::post('/logout', 'AuthController@logout');
});