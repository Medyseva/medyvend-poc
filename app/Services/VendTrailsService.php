<?php

namespace App\Services;

use App\Models\VendingMachine;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VendTrailsService
{
    protected $baseUrl = 'https://emapi.vendtrails.com/api';

    protected function getMachineTokens($machineNum): ?VendingMachine
    {
        return VendingMachine::where('machine_num', $machineNum)->first();
    }

    public function generateNewToken($machineNum, $companyNum = 1)
    {
        try {
            $response = Http::asForm()->post("{$this->baseUrl}/generate_token", [
                'company_num' => $companyNum
            ]);

            if ($response->successful()) {
                $tokens = $response->json('token');
                
                if (!$tokens || !isset($tokens['accessToken'])) {
                    throw new \Exception("Invalid token response format");
                }

                VendingMachine::where('machine_num', $machineNum)->update([
                    'vendtrails_access_token' => $tokens['accessToken'],
                    'vendtrails_refresh_token' => $tokens['refreshToken'] ?? null,
                ]);

                Log::channel('vending_trails')->info("New token generated for machine {$machineNum}");
                return $tokens['accessToken'];
            }

            throw new \Exception("Token generation failed: " . $response->body());
        } catch (\Exception $e) {
            Log::channel('vending_trails')->error("Failed to generate token for machine {$machineNum}: " . $e->getMessage());
            throw $e;
        }
    }

    public function sendInstructionToMachine($machineNum, $companyNum, $instructions)
    {
        $machine = $this->getMachineTokens($machineNum);
        
        if (!$machine) {
            throw new \Exception("Machine {$machineNum} not found");
        }

        $accessToken = $machine->vendtrails_access_token;

        if (!$accessToken) {
            Log::channel('vending_trails')->info("No access token found for machine {$machineNum}. Generating new token.");
            $accessToken = $this->generateNewToken($machineNum, $companyNum);
        }

        $payload = [
            'machine_num' => $machineNum,
            'company_num' => $companyNum,
            'ins_json' => $instructions
        ];

        Log::channel('vending_trails')->debug("Sending instruction to machine {$machineNum}", [
            'payload' => $payload
        ]);

        $response = $this->makeAuthenticatedRequest('POST', '/sendInstructionToMachine', $payload, $machineNum, $companyNum);

        $body = $response->json();
        Log::channel('vending_trails')->debug("Instruction API response", ['body' => $body]);

        if (!isset($body['task_num'])) {
            throw new \Exception("VendTrails did not return a task number. Response: " . json_encode($body));
        }

        return $body['task_num'];
    }

    public function checkMachineStatus($taskNum, $machineNum)
    {
        $machine = $this->getMachineTokens($machineNum);
        
        if (!$machine || !$machine->vendtrails_access_token) {
            throw new \Exception("No valid token found for machine {$machineNum}");
        }

        $payload = ['task_num' => $taskNum];

        $response = $this->makeAuthenticatedRequest('POST', '/checkMachineStatus', $payload, $machineNum);

        $body = $response->json();
        $msg = $body['msg'] ?? 'No response';
        
        Log::channel('vending_trails')->info("checkMachineStatus result for task_num {$taskNum}: {$msg}");

        // Check for various success indicators
        $isCompleted = in_array(strtolower($msg), [
            'task is succussfully completed',
            'task is successfully completed',
            'completed',
            'success'
        ]);

        return $isCompleted;
    }

    public function hardResetMachine($machineNum)
    {
        $machine = $this->getMachineTokens($machineNum);
        
        if (!$machine || !$machine->vendtrails_access_token) {
            throw new \Exception("No valid token found for machine {$machineNum}");
        }

        $payload = ['machine_num' => $machineNum];

        $response = $this->makeAuthenticatedRequest('POST', '/machineHardReset', $payload, $machineNum);

        $body = $response->json();
        $msg = $body['msg'] ?? 'No response';
        
        Log::channel('vending_trails')->info("Hard reset response for machine {$machineNum}: {$msg}");

        return $msg;
    }

    public function getMachineDetails($machineNum)
    {
        $machine = $this->getMachineTokens($machineNum);
        
        if (!$machine || !$machine->vendtrails_access_token) {
            throw new \Exception("No valid token found for machine {$machineNum}");
        }

        $payload = ['machine_num' => $machineNum];

        $response = $this->makeAuthenticatedRequest('POST', '/getMachineDetails', $payload, $machineNum);

        return $response->json();
    }

    protected function makeAuthenticatedRequest($method, $endpoint, $payload, $machineNum, $companyNum = 1)
    {
        $machine = $this->getMachineTokens($machineNum);
        $accessToken = $machine->vendtrails_access_token;

        $headers = [
            'Authorization' => $accessToken,
            'Content-Type' => 'application/json'
        ];

        // First attempt
        $response = Http::withHeaders($headers)->$method("{$this->baseUrl}{$endpoint}", $payload);

        // If unauthorized, try to refresh token and retry
        if ($response->status() === 403 || str_contains($response->body(), 'Unauthenticated')) {
            Log::channel('vending_trails')->warning("Unauthorized request for machine {$machineNum}. Attempting to generate new token.");
            
            $accessToken = $this->generateNewToken($machineNum, $companyNum);
            $headers['Authorization'] = $accessToken;

            $response = Http::withHeaders($headers)->$method("{$this->baseUrl}{$endpoint}", $payload);
        }

        if (!$response->successful()) {
            throw new \Exception("VendTrails API error: " . $response->status() . " - " . $response->body());
        }

        return $response;
    }

    public function getHealthStatus()
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/health");
            return $response->successful();
        } catch (\Exception $e) {
            Log::channel('vending_trails')->error("VendTrails health check failed: " . $e->getMessage());
            return false;
        }
    }
}