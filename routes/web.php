<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tables', function () {
	Schema::drop('migrations');
	Schema::drop('searches');
	Schema::drop('chats');
    return $tables = DB::select('SHOW TABLES'); // returns an array of stdObjects
});


// [
// 	{
// 		Tables_in_autopartz_new: "admin"
// 	},
// 	{
// 		Tables_in_autopartz_new: "ads_gallery"
// 	},
// 	{
// 		Tables_in_autopartz_new: "advertisement"
// 	},
// 	{
// 		Tables_in_autopartz_new: "association"
// 	},
// 	{
// 		Tables_in_autopartz_new: "banner"
// 	},
// 	{
// 		Tables_in_autopartz_new: "brand"
// 	},
// 	{
// 		Tables_in_autopartz_new: "cannot_find_searhing"
// 	},
// 	{
// 		Tables_in_autopartz_new: "cars"
// 	},
// 	{
// 		Tables_in_autopartz_new: "categories"
// 	},
// 	{
// 		Tables_in_autopartz_new: "category"
// 	},
// 	{
// 		Tables_in_autopartz_new: "city"
// 	},
// 	{
// 		Tables_in_autopartz_new: "cms"
// 	},
// 	{
// 		Tables_in_autopartz_new: "comments"
// 	},
// 	{
// 		Tables_in_autopartz_new: "comments_on"
// 	},
// 	{
// 		Tables_in_autopartz_new: "component"
// 	},
// 	{
// 		Tables_in_autopartz_new: "component_category"
// 	},
// 	{
// 		Tables_in_autopartz_new: "component_group"
// 	},
// 	{
// 		Tables_in_autopartz_new: "component_image"
// 	},
// 	{
// 		Tables_in_autopartz_new: "component_supplier"
// 	},
// 	{
// 		Tables_in_autopartz_new: "conditions"
// 	},
// 	{
// 		Tables_in_autopartz_new: "contact"
// 	},
// 	{
// 		Tables_in_autopartz_new: "contact_user"
// 	},
// 	{
// 		Tables_in_autopartz_new: "countries"
// 	},
// 	{
// 		Tables_in_autopartz_new: "country"
// 	},
// 	{
// 		Tables_in_autopartz_new: "country_phone_code"
// 	},
// 	{
// 		Tables_in_autopartz_new: "event"
// 	},
// 	{
// 		Tables_in_autopartz_new: "feedback"
// 	},
// 	{
// 		Tables_in_autopartz_new: "feedback_comment"
// 	},
// 	{
// 		Tables_in_autopartz_new: "flag_review"
// 	},
// 	{
// 		Tables_in_autopartz_new: "groups"
// 	},
// 	{
// 		Tables_in_autopartz_new: "issue"
// 	},
// 	{
// 		Tables_in_autopartz_new: "issue_skill"
// 	},
// 	{
// 		Tables_in_autopartz_new: "location"
// 	},
// 	{
// 		Tables_in_autopartz_new: "logs"
// 	},
// 	{
// 		Tables_in_autopartz_new: "make"
// 	},
// 	{
// 		Tables_in_autopartz_new: "make_repairer_junction"
// 	},
// 	{
// 		Tables_in_autopartz_new: "manage_banner"
// 	},
// 	{
// 		Tables_in_autopartz_new: "model"
// 	},
// 	{
// 		Tables_in_autopartz_new: "news_category"
// 	},
// 	{
// 		Tables_in_autopartz_new: "origincountry"
// 	},
// 	{
// 		Tables_in_autopartz_new: "ratesite"
// 	},
// 	{
// 		Tables_in_autopartz_new: "rating"
// 	},
// 	{
// 		Tables_in_autopartz_new: "reason"
// 	},
// 	{
// 		Tables_in_autopartz_new: "recently_viewed"
// 	},
// 	{
// 		Tables_in_autopartz_new: "recommend"
// 	},
// 	{
// 		Tables_in_autopartz_new: "repairer"
// 	},
// 	{
// 		Tables_in_autopartz_new: "repairer_rating"
// 	},
// 	{
// 		Tables_in_autopartz_new: "review"
// 	},
// 	{
// 		Tables_in_autopartz_new: "saveduser"
// 	},
// 	{
// 		Tables_in_autopartz_new: "searchingfor"
// 	},
// 	{
// 		Tables_in_autopartz_new: "send_invoice"
// 	},
// 	{
// 		Tables_in_autopartz_new: "settings"
// 	},
// 	{
// 		Tables_in_autopartz_new: "skills"
// 	},
// 	{
// 		Tables_in_autopartz_new: "skills_junction"
// 	},
// 	{
// 		Tables_in_autopartz_new: "smstemplate"
// 	},
// 	{
// 		Tables_in_autopartz_new: "state"
// 	},
// 	{
// 		Tables_in_autopartz_new: "suplier_registration"
// 	},
// 	{
// 		Tables_in_autopartz_new: "template"
// 	},
// 	{
// 		Tables_in_autopartz_new: "thumbs"
// 	},
// 	{
// 		Tables_in_autopartz_new: "trim"
// 	},
// 	{
// 		Tables_in_autopartz_new: "trims"
// 	},
// 	{
// 		Tables_in_autopartz_new: "user"
// 	},
// 	{
// 		Tables_in_autopartz_new: "vechicle_ip"
// 	},
// 	{
// 		Tables_in_autopartz_new: "working_hours"
// 	},
// 	{
// 		Tables_in_autopartz_new: "year"
// 	},
// 	{
// 		Tables_in_autopartz_new: "years"
// 	}
// ]