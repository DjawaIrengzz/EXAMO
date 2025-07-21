<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\File;
use App\Models\Review;
class RepoController extends Controller
{
    public function store(Request $request){
        $request->validate([]);
        $user = $request->user();
        preg_match('/github\.com\/([\w\-]+)\/([\w\-]+)/', $request->repo_url, $matches);
        if(count($matches) < 3){
            return response()->json([
                'error' => 'url tidak valdi'
            ],422);
        }
        [$_,$githubUser,$repoName]=$matches;

        $apiUrl='https://api.github.com/repos/$githubUser/$repoName/git/trees/main?recursive=1';
        $response = Http::get( $apiUrl);
        if($response->getStatusCode() != 200){ 
            return response()->json([
                'error' => 'gagal ambik repok'
            ],400);

        }
        $tree = $response->json()['tree'];
        $repo = Repository::create([
            'user_id' => $user->id,
            'repo_name' =>$repoName,
            'repo_url' => $request -> repo_url,
            'retrieved_at' =>now()

        ]);
        foreach ($tree as $item){
            if($item['type']==='blob'){
                $path = $item['path'];
                if(Str::endsWith($path,['.php', '.py', '.ts', '.java'])){
                    File::create([
                        'repository_id' => $repo->id,
                        'path'=>$path,
                        'language' =>pathinfo($path, PATHINFO_EXTENSION),
                    ]);
            }
        }
        return response() ->json([
            'message'=>'berhasil', 'repository' => $repo
        ]);
    }
    }
    public function fetchContentReview($id){
        $repo = Repository::with('files')->findOrFail($id);
        foreach ($repo->files as $file){

            $url="https://raw.githubusercontent.com/" . $this -> githubPathFromUrl($repo->repo_url). "/main/". $file->path;
            $res = Http::get($url);
            if($res->ok()){
                $content = $res->body();
                $file->update(['content' => $content]);
                $score = rand(60,95);
                $feedback = $this -> generateDummyFeedback($file->language);

                Review::create([
                    'file_id'=> $repo->id,
                    'ai_score' => $score,
                    'ai_feedback' => $feedback
                ]);
                return response() ->json([
                    'message' => 'berhasil'
                ]);
            };

        }
   
    }
 private function githubPathFromUrl($url){
    preg_match('/github\.com\/([\w\-]+)\/([\w\-]+)/', $url, $matches);
    return $matches[1].'/'.$matches[2];
    }
private function generateDummyFeedback($lang){
    return match ($lang){
        'php' => "Pastikan menggunakan dependency injection & validasi request di setiap controller.",
        'js' => "Gunakan modularisasi file dan hindari callback hell.",
        'py' => "Tingkatkan penggunaan docstring & type hinting.",
        'java' => "Saran: gunakan interface untuk generalisasi dan pemisahan logic.",
        default => "Kodingan terlihat rapi, jaga konsistensi penulisan.",
    };
}
public function showReview($id){
    $repo = Repository::with(['files.review'])->findOrFail($id);
    $data = [
        'repo_name'=> $repo->repo_name,
        'description' =>$repo ->description,
        'files' => []
    ];   
    foreach ($repo->files as $file){
        $review = $file->review;
        $data['files'][]=[
            'path' => $file->path,
            'language' => $file ->language,
            'score' =>$review->ai_score??null,
            'feedback'=> $review->ai_feedback??null,
        ];

    }
    return response()->json($data);
}
}