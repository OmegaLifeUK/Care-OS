<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class UserQualification extends Model
{
    protected $table = 'user_qualification';

    protected $fillable = [
        'image',
        'name',
        'user_id',
        'course_id'
    ];

    // public static function saveQualification($data = [], $user_id = null)
    // {
    //     Log::info('saveQualification called', [
    //         'user_id' => $user_id,
    //         'data'    => $data
    //     ]);

    //     if (empty($data) || !$user_id) {
    //         Log::warning('No qualifications found or user_id missing');
    //         return false;
    //     }

    //     $saved = false;

    //     foreach ($data as $key => $qual) {

    //         Log::info("Processing qualification", ['key' => $key, 'qual' => $qual]);

    //         // ✅ Required checks
    //         if (
    //             empty($qual['course_id']) ||
    //             empty($qual['name'])
    //         ) {
    //             continue;
    //         }

    //         // ✅ File check
    //         if (
    //             !isset($qual['cert']) ||
    //             !$qual['cert'] instanceof \Illuminate\Http\UploadedFile
    //         ) {
    //             continue;
    //         }

    //         $file = $qual['cert'];
    //         $ext  = strtolower($file->getClientOriginalExtension());

    //         $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
    //         if (!in_array($ext, $allowed_ext)) {
    //             continue;
    //         }

    //         $new_name = time() . rand(111, 999) . '.' . $ext;

    //         $file->move(
    //             public_path('images/userQualification'),
    //             $new_name
    //         );

    //         UserQualification::create([
    //             'user_id'   => $user_id,
    //             'course_id' => $qual['course_id'],
    //             'name'      => $qual['name'],
    //             'image'     => $new_name,
    //         ]);

    //         $saved = true; // ✅ Mark success
    //     }

    //     return $saved;
    // }
    public static function saveQualification($data = [], $user_id = null)
    {
        // echo "<pre>";print_r($data);die;
        Log::info('saveQualification called', [
            'user_id' => $user_id,
            'data'    => $data
        ]);

        if (empty($data) || !$user_id) {
            Log::warning('No qualifications found or user_id missing');
            return false;
        }

        $saved = false;

        foreach ($data as $key=>$qual) {
            Log::info("Processing qualification", ['key' => $key, 'qual' => $qual]);
            if (empty($qual['course_id']) || empty($qual['name'])) {
                continue;
            }

            $imageName = null;
            if (
                isset($qual['cert']) &&
                $qual['cert'] instanceof \Illuminate\Http\UploadedFile
            ) {
                $file = $qual['cert'];
                $ext  = strtolower($file->getClientOriginalExtension());

                $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
                if (!in_array($ext, $allowed_ext)) {
                    continue;
                }

                $imageName = time() . rand(111, 999) . '.' . $ext;
                $file->move(public_path('images/userQualification'), $imageName);
            }
            if (!empty($qual['previous_id'])) {
                $updateData = [
                    'course_id' => $qual['course_id'],
                    'name'      => $qual['name'],
                ];

                if ($imageName) {
                    $updateData['image'] = $imageName;
                }

                UserQualification::where('id', $qual['previous_id'])
                    ->where('user_id', $user_id)
                    ->update($updateData);

                $saved = true;

            } else {
                UserQualification::create([
                    'user_id'   => $user_id,
                    'course_id' => $qual['course_id'],
                    'name'      => $qual['name'],
                    'image'     => $imageName ?? '',
                ]);

                $saved = true;
            }
        }

        return $saved;
    }
}
