<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Willywes\AgoraSDK\RtcTokenBuilder; // Import the RtcTokenBuilder class

class AgoraController extends Controller
{
    private $appId = '0690a11cd2964546986e6754d89369aa'; // Your Agora App ID
    private $appCertificate = '0af290dbd4d142efa6e9a01a3c936f71'; // Your Agora App Certificate

    public function generateToken(Request $request)
    {
        $channelName = $request->input('channelName');
        $uid = $request->input('uid'); // 0 for auto-generated UID
        $role = $request->input('role'); // 'publisher' or 'audience'

        // Validate the role
        $role = $this->validateRole($role);
        if (is_string($role)) {
            // Return validation error if the role is invalid
            return response()->json(['error' => $role], 400);
        }

        $token = $this->generateAgoraToken($channelName, $uid, $role);
        return response()->json(['token' => $token]);
    }

    private function validateRole($role)
    {
        // Map the role to the Agora SDK role constants
        if ($role === 'publisher') {
            return RtcTokenBuilder::RolePublisher;
        } elseif ($role === 'audience') {
            return RtcTokenBuilder::RoleSubscriber;
        } else {
            // Return error message as a string if the role is invalid
            return 'Invalid role';
        }
    }

    private function generateAgoraToken($channelName, $uid, $role)
    {
        $expiry = 3600; // Token expiry time (in seconds)
        $currentTimestamp = (new \DateTime("now", new \DateTimeZone('UTC')))->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expiry;

        // Generate the token using RtcTokenBuilder
        return RtcTokenBuilder::buildTokenWithUid(
            $this->appId,
            $this->appCertificate,
            $channelName,
            $uid,
            $role,
            $privilegeExpiredTs
        );
    }
}