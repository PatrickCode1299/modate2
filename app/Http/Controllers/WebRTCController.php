<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Pusher\Pusher;

class WebRTCController extends Controller
{
    protected $pusher;

    public function __construct()
    {
        $this->pusher = new Pusher(
            'd81e8911650d34cdc928',
            'dd283be2f933a17310dd',
            '1824187',
            [
                'cluster' => 'eu',
                'useTLS' => true
            ]
        );
    }

    public function generateLink(Request $request)
    {
        $link = url('/live/' . uniqid());
        return response()->json(['link' => $link]);
    }
    public function broadcastSignal(Request $request)
    {
        if (!$request->hasFile('videoChunk')) {
            return response()->json(['error' => 'No video chunk found'], 400);
        }
    
        $videoChunk = $request->file('videoChunk');
        $streamId = $request->input('streamId');
        $chunkIndex = $request->input('chunkIndex');
        $chunkPath = storage_path("app/public/live_video/{$streamId}_chunk_{$chunkIndex}.webm");
    
        // Save the video chunk
        file_put_contents($chunkPath, file_get_contents($videoChunk->getRealPath()));
    
        return response()->json(['status' => 'ok']);
    }
    public function signalPusher(Request $request){
        $streamId = $request->input('streamId');
        $filename = "live_video/{$streamId}.webm";
        $publicPath ="https://hexarex.com/storage/".$filename;
        $this->pusher->trigger('live-stream.' . $streamId, 'video-chunk', ['filePath' => $publicPath]);
        return response([
            "reply"=>"success",
            "streamId"=>$streamId
        ]);
    }
    public function deleteStreamVideos($streamId) {
    $directory =asset("storage/live_video/{$streamId}");

    if (is_dir($directory)) {
        $files = glob("{$directory}/*");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        rmdir($directory);
    }

    return response()->json(['message' => 'Stream videos deleted successfully']);
}
public function getVideoChunk(Request $request)
{
    $streamId = $request->input('streamId');
    $chunkIndex = $request->input('chunkIndex');
    $directory = storage_path('app/public/live_video');
    $chunkPath = $directory . "/{$streamId}_chunk_{$chunkIndex}.webm";

    if (!file_exists($chunkPath)) {
        return response()->json(['chunk' => null], 404);
    }

    return response()->json(['chunk' => asset('storage/live_video/' . basename($chunkPath))]);
}
}