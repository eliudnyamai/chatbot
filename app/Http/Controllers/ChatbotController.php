<?php

namespace App\Http\Controllers;

use App\Mail\AnswerRequest;
use App\Models\TrainingData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Threads\Runs\ThreadRunResponse;
use Twilio\Rest\Client;

set_time_limit(0);
class ChatbotController extends Controller
{
    public ?string $error = null;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trainingData = TrainingData::all();
        return view('chatbot.index')
            ->with('data', $trainingData);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'question' => 'required|string',
            'response' => 'required|string',
        ]);
        $pumbleKey = env('PUMBLE_KEY_TRAINER'); //when replying as trainer
        $SendMessagesUrl = 'https://pumble-api-keys.addons.marketplace.cake.com/sendReply';
        TrainingData::create($validatedData);
        if ($request->input("message_id")) {
            //dd($request->input("message_id"));
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Api-Key' => $pumbleKey
            ])->post($SendMessagesUrl, [
                'channel' => 'questions',
                'messageId' => $request->input('message_id'),
                'text' => $request->input('response'),
                'asBot' => true
            ]);

            if ($response->failed()) {
                Log::info("Failed to send message for ID: {$request->input('message_id')}");
            } else {
                Log::info("Successfully sent reply for ID: {$request->input('message_id')}");
            }
        }
        return redirect('/chatbot')->with('message', 'Training data stored successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = TrainingData::find($id);
        return view('chatbot.edit', ['item' => $item]);
    }
    public function create(Request $request)
    {
        $messageId = $request->input('message_id');
        $question = $request->input("question");
        return view('chatbot.create')->with('messageId', $messageId)->with('question', $question);
    }

    public function train_bot()
    {
        $trainingData = TrainingData::all();
        $jsonData = $trainingData->toJson(JSON_PRETTY_PRINT);
        $exportPath = 'public/training_data.json';
        if (Storage::exists($exportPath)) {
            Storage::delete($exportPath);
        }
        $files = OpenAI::files()->list();
        $filteredFiles = array_filter($files->data, function ($file) {
            return $file->filename === 'training_data.json';
        });
        foreach ($filteredFiles as $file) {
            OpenAI::files()->delete($file->id);
            OpenAI::assistantsFiles()->delete('asst_C0AgfIgmMHDMCYBOXYa8quPL', $file->id);
        }
        Storage::put($exportPath, $jsonData);
        $uploadedFile = OpenAI::files()->upload([
            'file' => Storage::disk('public')->readStream('training_data.json'),
            'purpose' => 'assistants',
        ]);
        $parameters = [
            'file_id' =>
            $uploadedFile->id
        ];
        OpenAI::assistantsFiles()->create('asst_C0AgfIgmMHDMCYBOXYa8quPL', $parameters);
        Log::info('Chatbot Training was successfull');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = TrainingData::find($id);
        if (!$item) {
            return redirect()->back()->with('message', 'Item not found.');
        }
        $item->update($request->all());
        return redirect()->back()->with('message', 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = trainingData::find($id);
        if (!$item) {
            return redirect()->back()->with('message', 'Item not found.');
        }
        $item->delete();
        return redirect()->back()->with('message', 'Item deleted successfully.');
    }
    public function replyToMessages()
    {
        set_time_limit(0);
        $pumbleKey = env('PUMBLE_KEY'); //when replying as michelle the bot
        $GetMessagesUrl = 'https://pumble-api-keys.addons.marketplace.cake.com/listMessages?channel=questions&Api-Key=' . $pumbleKey;
        // Make GET request with headers
        $response = Http::withHeaders([
            'Api-Key' => $pumbleKey,
        ])->get($GetMessagesUrl);
        if ($response->successful()) {
            $messages = $response->json()['messages'];
            $replierId = "66324a40a62d3a1d0c6b1891";
            $unrepliedMessages = [];
            foreach ($messages as $message) {
                if (isset($message['threadRootInfo']) && isset($message['threadRootInfo']['repliers'])) {
                    if (!in_array($replierId, $message['threadRootInfo']['repliers'])) {
                        $unrepliedMessages[] = $message;  // Add to results if replier is not found
                    }
                } else {
                    $unrepliedMessages[] = $message;
                }
            }
            $SendMessagesUrl = 'https://pumble-api-keys.addons.marketplace.cake.com/sendReply';
            foreach ($unrepliedMessages as $message) {
                $url = url('/create?message_id=' . $message['id'] . '&question=' . urlencode($message['text']));
                $body = "Click this link <a href={$url}>Answer</a> to provide an answer to your teammate";
                $this->sendSMS($body);

                $text = $message['text'];
                $threadRun = $this->createAndRunThread($text);
                $answer = $this->loadAnswer($threadRun);
                if ($answer == "Null") {
                    $url = url('/create?message_id=' . $message['id'] . '&question=' . urlencode($message['text']));
                    $body = "Click this link <a href={$url}>Answer</a> to provide an answer to your teammate";
                    $answer ="Just a minute";
                    $this->sendSMS($body);
                }
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Api-Key' => $pumbleKey
                ])->post($SendMessagesUrl, [
                    'channel' => 'questions',
                    'messageId' => $message['id'],
                    'text' => $answer,
                    'asBot' => true
                ]);
                if ($response->failed()) {
                    // Handle failure case
                    Log::info("Failed to send message for ID: {$message['id']}\n");
                } else {
                    // Handle successful case
                    Log::info("Successfully sent reply for ID: {$message['id']}\n");
                }
            }
        } else {
            Log::error('Failed to retrieve messages: ' . $response->status());
            return response()->json(['error' => 'Failed to retrieve messages'], $response->status());
        }
    }
    private function loadAnswer(ThreadRunResponse $threadRun)
    {
        set_time_limit(0);
        while (in_array($threadRun->status, ['queued', 'in_progress'])) {
            $threadRun = OpenAI::threads()->runs()->retrieve(
                threadId: $threadRun->threadId,
                runId: $threadRun->id,
            );
        }

        if ($threadRun->status !== 'completed') {
            $this->error = 'Request failed, please try again';
        }

        $messageList = OpenAI::threads()->messages()->list(
            threadId: $threadRun->threadId,
        );

        return $messageList->data[0]->content[0]->text->value;
    }
    private function createAndRunThread($question): ThreadRunResponse
    {
        set_time_limit(0);
        return OpenAI::threads()->createAndRun([
            'assistant_id' => 'asst_C0AgfIgmMHDMCYBOXYa8quPL',
            'thread' => [
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $question,
                    ],
                ],
            ],
        ]);
    }
    private function sendSMS($body)
    {
        set_time_limit(0);
        $account_sid = getenv('TWILIO_ACCOUNT_SID');
        $auth_token = getenv('TWILIO_AUTH_TOKEN');

        $twilio_number = "+1 833 987 4856";

        $client = new Client($account_sid, $auth_token);
        $client->messages->create(
            // Where to send a text message (your cell phone?)
            '689-287-3119',
            array(
                'from' => $twilio_number,
                'body' => $body
            )
        );
    }
}
