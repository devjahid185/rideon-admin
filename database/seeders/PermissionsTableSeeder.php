<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id' => 1,
                'title' => 'user_management_access',
            ],
            [
                'id' => 2,
                'title' => 'permission_create',
            ],
            [
                'id' => 3,
                'title' => 'permission_edit',
            ],
            [
                'id' => 4,
                'title' => 'permission_show',
            ],
            [
                'id' => 5,
                'title' => 'permission_delete',
            ],
            [
                'id' => 6,
                'title' => 'permission_access',
            ],
            [
                'id' => 7,
                'title' => 'role_create',
            ],
            [
                'id' => 8,
                'title' => 'role_edit',
            ],
            [
                'id' => 9,
                'title' => 'role_show',
            ],
            [
                'id' => 10,
                'title' => 'role_delete',
            ],
            [
                'id' => 11,
                'title' => 'role_access',
            ],
            [
                'id' => 12,
                'title' => 'user_create',
            ],
            [
                'id' => 13,
                'title' => 'user_edit',
            ],
            [
                'id' => 14,
                'title' => 'user_show',
            ],
            [
                'id' => 15,
                'title' => 'user_delete',
            ],
            [
                'id' => 16,
                'title' => 'user_access',
            ],
            [
                'id' => 17,
                'title' => 'item_setting_access',
            ],
            [
                'id' => 18,
                'title' => 'slider_create',
            ],
            [
                'id' => 19,
                'title' => 'slider_edit',
            ],
            [
                'id' => 20,
                'title' => 'slider_show',
            ],
            [
                'id' => 21,
                'title' => 'slider_delete',
            ],
            [
                'id' => 22,
                'title' => 'slider_access',
            ],
            [
                'id' => 23,
                'title' => 'faq_management_access',
            ],
            [
                'id' => 24,
                'title' => 'faq_category_create',
            ],
            [
                'id' => 25,
                'title' => 'faq_category_edit',
            ],
            [
                'id' => 26,
                'title' => 'faq_category_show',
            ],
            [
                'id' => 27,
                'title' => 'faq_category_delete',
            ],
            [
                'id' => 28,
                'title' => 'faq_category_access',
            ],
            [
                'id' => 29,
                'title' => 'faq_question_create',
            ],
            [
                'id' => 30,
                'title' => 'faq_question_edit',
            ],
            [
                'id' => 31,
                'title' => 'faq_question_show',
            ],
            [
                'id' => 32,
                'title' => 'faq_question_delete',
            ],
            [
                'id' => 33,
                'title' => 'faq_question_access',
            ],
            [
                'id' => 34,
                'title' => 'city_create',
            ],
            [
                'id' => 35,
                'title' => 'city_edit',
            ],
            [
                'id' => 36,
                'title' => 'city_show',
            ],
            [
                'id' => 37,
                'title' => 'city_access',
            ],
            [
                'id' => 38,
                'title' => 'property_type_create',
            ],
            [
                'id' => 39,
                'title' => 'property_type_edit',
            ],
            [
                'id' => 40,
                'title' => 'property_type_show',
            ],
            [
                'id' => 41,
                'title' => 'property_type_access',
            ],
            [
                'id' => 42,
                'title' => 'space_type_create',
            ],
            [
                'id' => 43,
                'title' => 'space_type_edit',
            ],
            [
                'id' => 44,
                'title' => 'space_type_show',
            ],
            [
                'id' => 45,
                'title' => 'space_type_access',
            ],
            [
                'id' => 46,
                'title' => 'bed_type_create',
            ],
            [
                'id' => 47,
                'title' => 'bed_type_edit',
            ],
            [
                'id' => 48,
                'title' => 'bed_type_show',
            ],
            [
                'id' => 49,
                'title' => 'bed_type_access',
            ],
            [
                'id' => 50,
                'title' => 'amenity_create',
            ],
            [
                'id' => 51,
                'title' => 'amenity_edit',
            ],
            [
                'id' => 52,
                'title' => 'amenity_show',
            ],
            [
                'id' => 53,
                'title' => 'amenity_access',
            ],
            [
                'id' => 54,
                'title' => 'app_user_create',
            ],
            [
                'id' => 55,
                'title' => 'app_user_edit',
            ],
            [
                'id' => 56,
                'title' => 'app_user_show',
            ],
            [
                'id' => 57,
                'title' => 'app_user_access',
            ],
            [
                'id' => 58,
                'title' => 'front_management_access',
            ],
            [
                'id' => 59,
                'title' => 'property_create',
            ],
            [
                'id' => 60,
                'title' => 'property_edit',
            ],
            [
                'id' => 61,
                'title' => 'property_show',
            ],
            [
                'id' => 62,
                'title' => 'property_delete',
            ],
            [
                'id' => 63,
                'title' => 'property_access',
            ],
            [
                'id' => 64,
                'title' => 'property_management_access',
            ],
            [
                'id' => 65,
                'title' => 'availability_create',
            ],
            [
                'id' => 66,
                'title' => 'availability_edit',
            ],
            [
                'id' => 67,
                'title' => 'availability_show',
            ],
            [
                'id' => 68,
                'title' => 'availability_access',
            ],
            [
                'id' => 69,
                'title' => 'testimonial_create',
            ],
            [
                'id' => 70,
                'title' => 'testimonial_edit',
            ],
            [
                'id' => 71,
                'title' => 'testimonial_show',
            ],
            [
                'id' => 72,
                'title' => 'testimonial_delete',
            ],
            [
                'id' => 73,
                'title' => 'testimonial_access',
            ],
            [
                'id' => 74,
                'title' => 'booking_create',
            ],
            [
                'id' => 75,
                'title' => 'booking_edit',
            ],
            [
                'id' => 76,
                'title' => 'booking_show',
            ],
            [
                'id' => 77,
                'title' => 'booking_access',
            ],
            [
                'id' => 78,
                'title' => 'review_create',
            ],
            [
                'id' => 79,
                'title' => 'review_edit',
            ],
            [
                'id' => 80,
                'title' => 'review_show',
            ],
            [
                'id' => 81,
                'title' => 'review_access',
            ],
            [
                'id' => 82,
                'title' => 'static_page_create',
            ],
            [
                'id' => 83,
                'title' => 'static_page_edit',
            ],
            [
                'id' => 84,
                'title' => 'static_page_show',
            ],
            [
                'id' => 85,
                'title' => 'static_page_delete',
            ],
            [
                'id' => 86,
                'title' => 'static_page_access',
            ],
            [
                'id' => 87,
                'title' => 'package_access',
            ],
            [
                'id' => 88,
                'title' => 'all_package_create',
            ],
            [
                'id' => 89,
                'title' => 'all_package_edit',
            ],
            [
                'id' => 90,
                'title' => 'all_package_show',
            ],
            [
                'id' => 91,
                'title' => 'all_package_access',
            ],
            [
                'id' => 92,
                'title' => 'general_setting_edit',
            ],
            [
                'id' => 93,
                'title' => 'general_setting_access',
            ],
            [
                'id' => 94,
                'title' => 'all_general_setting_access',
            ],
            [
                'id' => 95,
                'title' => 'coupon_access',
            ],
            [
                'id' => 96,
                'title' => 'add_coupon_create',
            ],
            [
                'id' => 97,
                'title' => 'add_coupon_edit',
            ],
            [
                'id' => 98,
                'title' => 'add_coupon_show',
            ],
            [
                'id' => 99,
                'title' => 'add_coupon_delete',
            ],
            [
                'id' => 100,
                'title' => 'add_coupon_access',
            ],
            [
                'id' => 101,
                'title' => 'payout_create',
            ],
            [
                'id' => 102,
                'title' => 'payout_edit',
            ],
            [
                'id' => 103,
                'title' => 'payout_show',
            ],
            [
                'id' => 104,
                'title' => 'payout_access',
            ],
            [
                'id' => 105,
                'title' => 'profile_password_edit',
            ],
        ];

        Permission::insert($permissions);
    }
}
