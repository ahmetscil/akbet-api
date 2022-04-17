<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use App\Http\Requests\UploadRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Image;

class UploadFile
{
    use AsAction;
    public function asController(Request $request)
    {
        return $this->handle($request);
    }
  
    public function handle($request)
    {
        if ($request->file('uploadFile')->isValid()) {
            $file = $request->uploadFile;
      
            $fileName = $file->getClientOriginalName();
            $fileExtension = $request->uploadFile->extension();
            $fileNewName = Str::slug($fileName, '-').'-'.time().'.'.$fileExtension;
      
            $small = Image::make($file)->resize(468, 468, function($constraint) { $constraint->aspectRatio(); })->encode($fileExtension);
            $normal = Image::make($file)->resize(768, 768, function($constraint) { $constraint->aspectRatio(); })->encode($fileExtension);
            $medium = Image::make($file)->resize(1024, 1024, function($constraint) { $constraint->aspectRatio(); })->encode($fileExtension);
            $large = Image::make($file)->resize(1920, 1920, function($constraint) { $constraint->aspectRatio(); })->encode($fileExtension);
            $img = $fileNewName;
            $imgmd = 'md/'.$fileNewName;
            $imgsm = 'sm/'.$fileNewName;
            $imglg = 'lg/'.$fileNewName;

            Storage::disk('public')->put('uploads/'.$img, (string)$normal, 'public');
            Storage::disk('public')->put('uploads/'.$imgmd, (string)$medium, 'public');
            Storage::disk('public')->put('uploads/'.$imgsm, (string)$small, 'public');
            Storage::disk('public')->put('uploads/'.$imglg, (string)$large, 'public');
      
            $data = [
              'url'=>$imglg,
              'name'=>$fileNewName
            ];
            
            $gal = [
                'project' => $request->project,
                'tag' => $request->tag,
                'title' => $fileName,
                'user' => $request->user,
                'photo' => $imglg,
                'created_at' => Pariette::now()
            ];
    
            DB::table('galleries')->insert($gal);
    
                return Hermes::send($data, 200);
          }
    }
}
