<?php

namespace App\Filament\Pages;

use App\Http\Controllers\EmailController;
use App\Models\Server;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification;

class SubscriptionForm extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static string $view = 'filament.pages.subscription-form';
    protected static ?string $navigationLabel = 'Subscription Email';
    protected ?string $heading = 'Send Subscription Email';
    protected ?string $subheading = 'Fill in the form below to send Subscription Email';
    protected static ?string $title = 'Subscription Email';

    public $server;
    public $connections = 1;
    public $subscription_type = 'new';
    public $duration = '1';
    public $client_email;
    public $client_full_name;
    public $user_pass = [];


    public function mount()
    {
        // for first time page load fill user_pass repeater with empty array
        $this->user_pass = array_fill(0, 1, ['username' => '', 'password' => '']);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Grid::make([
                'default' => 2,
            ])->schema([
                Forms\Components\Select::make('server')
                    ->options(
                        Server::all()->pluck('name', 'id')
                    )
                    ->required()
                    ->label('Select Server')
                    ->placeholder('Select Server')
                    ->helperText('Select IPTV Server')
                    ->columnSpan(1)
                    ->native(false),


                Forms\Components\Select::make('subscription_type')
                    ->options([
                        'new' => 'New Subscription',
                        'renewal' => 'Renewal',
                    ])
                    ->default('new')
                    ->required()
                    ->label('Subscription Type')
                    ->helperText('Select whether its a New subscription or a Renewal')
                    ->placeholder('Select Subscription Type')
                    ->selectablePlaceholder(false)
                    ->native(false)
                    ->live(),

                Forms\Components\Select::make('connections')
                    ->options([
                        '1' => '1 Connection',
                        '2' => '2 Connections',
                        '3' => '3 Connections',
                    ])
                    ->required()
                    ->label('Number of Connections')
                    ->placeholder('Select Number of Connections')
                    ->helperText('Select Number of Connections')
                    ->live()
                    ->default('1')
                    ->afterStateUpdated(function ($state) {
                        // $state has connections value
                        $this->updateUserPass($state);
                    })
                    ->native(false),
//                    ->hidden(fn(Get $get) => $get('subscription_type') != 'new'),

                Forms\Components\Select::make('duration')
                    ->options([
                        '1' => '1 Month',
                        '3' => '3 Months',
                        '6' => '6 Months',
                        '12' => 'Annual',
                        '24' => 'Bi-Annual',
                    ])
                    ->required()
                    ->label('Choose Duration')
                    ->helperText('Select Subscription Duration')
                    ->default( '1' )
                    ->selectablePlaceholder(false)
                    ->native(false),


                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('client_full_name')
                        ->required()
                        ->label('Customer\'s Full Name')
                        ->columnSpan('1'),

                    Forms\Components\TextInput::make('client_email')
                        ->required()
                        ->label('Customer\'s Email')
                        ->email()
                        ->columnSpan('1'),
                ])
                ->columns('2')
                ->columnSpan('full'),

                Forms\Components\Repeater::make('user_pass')->schema([
                    Forms\Components\TextInput::make('username')
                        ->required()
                        ->label('Username'),

                    Forms\Components\TextInput::make('password')
                        ->required()
                        ->label('Password')
                ])
                ->required()
                ->label('Enter Details for All Connections')
                ->reorderable(false)
                ->addable(false)
                ->deletable(false)
                ->columns(2)
                ->columnSpan('full')
        ])
        ];
    }
    public function updateUserPass($connections)
    {
        // Update fill empty user_pass array based on the number of connections
        $this->user_pass = array_fill(0, $connections, ['username' => '', 'password' => '']);
        // Reset the form state
        $this->form->user_pass = $this->user_pass;
    }

    public function submitForm()
    {
        $data = $this->form->getState();
        $result = EmailController::send($data);
        if($result === true){
            // Send a success notification
            Notification::make()
                ->title( 'Success')
                ->body('Subscription Email sent successfully!')
                ->success()
                ->seconds(10)
                ->send();
            // Reset the form
            $this->form->fill();
        }else{
            Notification::make()
                ->title( 'Error Sending Subscription Email!')
                ->body($result)
                ->danger()
                ->send();
        }
    }
}
