<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('value')->default('0');
            $table->timestamps();
        });
        $settings = array(
            array(
                "name" => "Booking Time Rule",
                "code" => "booking_time",
                "value" => "1"
            ),
            array(
                "name" => "Full Payment Discount",
                "code" => "full_discount",
                "value" => "0"
            ),
            array(
                "name" => "Page Logo",
                "code" => "page_logo",
                "value" => null
            ),
            array(
                "name" => "Page Favicon",
                "code" => "page_favicon",
                "value" => null
            ),
            array(
                "name" => "Page Copyright Text",
                "code" => "page_copyright_text",
                "value" => null
            ),
            array(
                "name" => "Page Copyright Link",
                "code" => "page_copyright_link",
                "value" => null
            ),
            array(
                "name" => "Page Help Text",
                "code" => "page_help_text",
                "value" => null
            ),
            array(
                "name" => "Page Help Link",
                "code" => "page_help_link",
                "value" => null
            ),
            array(
                "name" => "Page Contact Text",
                "code" => "page_contact_text",
                "value" => null
            ),
            array(
                "name" => "Page Contact Link",
                "code" => "page_contact_link",
                "value" => null
            ),
            array(
                "name" => "Page Facebook Link",
                "code" => "page_facebook_link",
                "value" => null
            ),
            array(
                "name" => "Page Twitter Link",
                "code" => "page_twitter_link",
                "value" => null
            ),
            array(
                "name" => "Page LinkedIn Link",
                "code" => "page_linkedin_link",
                "value" => null
            ),
            array(
                "name" => "Page Instagram Link",
                "code" => "page_instagram_link",
                "value" => null
            ),
            array(
                "name" => "Page Email",
                "code" => "page_email",
                "value" => null
            ),
            array(
                "name" => "Page Payment Logo",
                "code" => "page_payment_logo",
                "value" => null
            ),
            array(
                "name" => "Page Payment Link",
                "code" => "page_payment_link",
                "value" => null
            ),
            array(
                "name" => "Homepage Title",
                "code" => "homepage_title",
                "value" => null
            ),
            array(
                "name" => "Homepage Description",
                "code" => "homepage_description",
                "value" => null
            ),
            array(
                "name" => "Homepage Button Text",
                "code" => "homepage_button_text",
                "value" => null
            ),
            array(
                "name" => "Homepage Slider 1",
                "code" => "homepage_slider_1",
                "value" => null
            ),
            array(
                "name" => "Homepage Slider 2",
                "code" => "homepage_slider_2",
                "value" => null
            ),
            array(
                "name" => "Homepage Slider 3",
                "code" => "homepage_slider_3",
                "value" => null
            ),
            array(
                "name" => "Homepage Slider 4",
                "code" => "homepage_slider_4",
                "value" => null
            ),
            array(
                "name" => "Homepage Slider 5",
                "code" => "homepage_slider_5",
                "value" => null
            ),
        );
        DB::table('settings')->insert($settings);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
