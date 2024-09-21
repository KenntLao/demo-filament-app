<?php

namespace App\Filament\Resources\TodoResource\Pages;

use App\Filament\Resources\TodoResource;
use Filament\Actions;
use App\Models\Todo;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;

class ListTodos extends ListRecords
{
    protected static string $resource = TodoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('syncFromApi')
                ->label('Sync Todos from API')
                ->action('syncFromApi'),
        ];
    }

    public function syncFromApi()
    {
        $response = Http::get('https://jsonplaceholder.typicode.com/todos');

        if ($response->successful()) {
            $todos = $response->json();

            foreach ($todos as $todo) {
                Todo::updateOrCreate(
                    [
                        'title' => $todo['title'],
                        'completed' => $todo['completed'],
                        'user_id' => auth()->user()->id,
                    ]
                );
            }
            
            Notification::make()
                ->title('ToDos synced from API!')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to sync ToDos from API.')
                ->danger()
                ->send();
        }
    }

}
