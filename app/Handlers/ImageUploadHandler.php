<?php

namespace App\Handlers;

use Image;
use Str;

class ImageUploadHandler
{
    protected $allowed_ext = ["png", "jpg", "gif", "jpeg"];

    public function save($file, $folder, $file_prefix, $max_width = false)
    {
        //建立儲存圖片的資料夾規則，例如：uploads/images/avatars/202010/22/
        //資料夾切割可讓找尋圖片的效率更高
        $folder_name = "uploads/images/$folder/" . date('Ym/d', time());

        //儲存圖片的實際路徑( public_path() 是 public資料夾 的路徑 )
        //例如： /home/vagrant/code/larabbs/public/uploads/images/avatars/202010/22/
        $upload_path = public_path() . '/' . $folder_name;

        //獲取圖檔的副檔名 (若圖檔沒有副檔名，此處確保副檔名一直存在)
        //若圖檔有副檔名，則取其副檔名 ( $file->getClientOriginalExtension() )
        //若圖檔沒有副檔名，則設定為png
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';
        // dd($extension);

        //連接檔案名稱，加前綴是為了增加辨識度，前綴可以是相關數據模型的 ID
        //例如：1_1493521050_7BVc9v9ujP.png
        $filename = $file_prefix . '_' . time() . '_' . Str::random(10) . '.' . $extension;

        //如果上傳的不是圖片，則終止操作
        if(! in_array($extension, $this->allowed_ext)){
            return false;
        }

        //把圖片移到自己設定的路徑
        $file->move($upload_path, $filename);

        //如果限制了圖片寬度，就執行圖片裁剪
        if($max_width && $extension != 'gif'){
            //此類別封裝的函數，用於執行圖片裁剪
            $this->reduceSize($upload_path . '/' . $filename, $max_width);
        }

        return [
            'path' => config('app.url') . "/$folder_name/$filename"
        ];
    }

    public function reduceSize($file_path, $max_width)
    {
        //先實例化，參數是圖片的路徑
        $image =Image::make($file_path);

        //調整圖片尺寸
        $image->resize($max_width, null, function($constraint){
            //設定寬度為 $max_width，高度為等比例縮放
            $constraint->aspectRatio();

            //防止裁圖時圖片尺寸變大
            $constraint->upsize();
        });

        //儲存已經調整完成的圖片
        $image->save();
    }
}
