<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;

class SettingsController extends Controller
{
    public function gradingScale()
    {
        // Standard Nigerian Grading Scale
        // In the future, this can be fetched from the DB per school
        $scale = [
            ['grade' => 'A', 'min' => 70, 'max' => 100, 'remark' => 'Excellent'],
            ['grade' => 'B', 'min' => 60, 'max' => 69, 'remark' => 'Very Good'],
            ['grade' => 'C', 'min' => 50, 'max' => 59, 'remark' => 'Credit'],
            ['grade' => 'D', 'min' => 45, 'max' => 49, 'remark' => 'Pass'],
            ['grade' => 'E', 'min' => 40, 'max' => 44, 'remark' => 'Fair'],
            ['grade' => 'F', 'min' => 0,  'max' => 39, 'remark' => 'Fail'],
        ];

        return ResponseHelper::success($scale, 'Grading scale fetched successfully');
    }

    public function getEnums(Request $request)
    {
        $type = $request->query('type');
        $data = [];

        switch ($type) {
            case 'gender':
                $data = [
                    ['id' => 'male', 'name' => 'Male'],
                    ['id' => 'female', 'name' => 'Female'],
                    ['id' => 'other', 'name' => 'Other'],
                ];
                break;
            case 'relation':
                $data = [
                    ['id' => 'father', 'name' => 'Father'],
                    ['id' => 'mother', 'name' => 'Mother'],
                    ['id' => 'uncle', 'name' => 'Uncle'],
                    ['id' => 'aunt', 'name' => 'Aunt'],
                    ['id' => 'grandfather', 'name' => 'Grandfather'],
                    ['id' => 'grandmother', 'name' => 'Grandmother'],
                    ['id' => 'other', 'name' => 'Other'],
                ];
                break;
            case 'status':
                $data = [
                    ['id' => 'active', 'name' => 'Active'],
                    ['id' => 'inactive', 'name' => 'Inactive'],
                ];
                break;
            case 'assignment_status':
                $data = [
                    ['id' => 'active', 'name' => 'Active'],
                    ['id' => 'draft', 'name' => 'Draft'],
                    ['id' => 'closed', 'name' => 'Closed'],
                ];
                break;
            default:
                return ResponseHelper::error('Invalid enum type', 400);
        }

        return ResponseHelper::success($data, 'Enums fetched successfully');
    }
}
