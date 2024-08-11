<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Wiebenieuwenhuis\FilamentCodeEditor\Components\CodeEditor;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;
    protected static ?int $navigationSort = 30;
    protected static ?string $navigationIcon = 'heroicon-o-code-bracket-square';
    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('server_id')
                    ->relationship('server', 'name')
                    ->label('Server')
                    ->placeholder('Select Server')
                    ->required(),

                Forms\Components\TextInput::make('heading_new')
                    ->label('Heading New Connection')
                    ->required()
                    ->columnSpan(1)
                    ->hint('HTML is supported'),

                Forms\Components\TextInput::make('paragraph_new')
                    ->label('Paragraph For New Connection')
                    ->required()
                    ->columnSpan(1)
                    ->hint('HTML is supported'),

                Forms\Components\TextInput::make('heading_renew')
                    ->label('Heading for Renewal')
                    ->required()
                    ->columnSpan(1)
                    ->hint('HTML is supported'),

                Forms\Components\TextInput::make('paragraph_renew')
                    ->label('Paragraph For Renewal')
                    ->required()
                    ->columnSpan(1)
                    ->hint('HTML is supported'),

                CodeEditor::make('template')
                    ->required()
                    ->label('Email Template')
                    ->hint('You can design new templates at unlayer.com')
                    ->helperText(
                        new HtmlString("This is the email template that will be sent to the user. Use {{var}} placeholders for dynamic content.
                      <br><h5>For example: </h5>
                      <b>{{heading}}</b> will be replaced with the heading of the email. <br>
                      <b>{{paragraph}}</b> will be replaced with the body paragraph based on subscription status. <br>
                      <b>{{username}}</b> will be replaced with the username of the user. <br>
                      <b>{{password}}</b> will be replaced with the password of the user. <br>
                      <b>{{client_full_name}}</b> will be replaced with the full name of the client. <br>
                      <b>{{client_email}}</b> will be replaced with the email of the client. <br>
                      ")
                    ),

            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('server.name')->label('Server Name'),
                Tables\Columns\TextColumn::make('created_at')->label('Created At'),
                Tables\Columns\TextColumn::make('updated_at')->label('Updated At'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                ReplicateAction::make()->form([
                  Forms\Components\Select::make('server_id')
                      ->relationship('server', 'name')
                      ->label('Server')
                      ->required(),
                ])->action(function ($record, array $data) {
                    $dup = $record->replicate();
                    $dup->server_id = $data['server_id'];
                    $dup->save();
                }),

                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->form([
                        Forms\Components\Select::make('subscription_type')
                        ->label('Select Type')
                        ->live()
                        ->options([
                            'new' => 'New Connection',
                            'renewal' => 'Renewal',
                        ]),

                        Forms\Components\Select::make('connections')
                        ->options([
                            '1' => '1 Connection',
                            '2' => '2 Connections',
                            '3' => '3 Connections',
                        ])
                        ->visible(fn(Get $get) => $get('subscription_type') == 'new')
                    ])
                    ->action(function ($record, array $data) {
                        redirect()->route('email-template.preview', ['emailTemplate' => $record->id, 'data' => $data]);
                    })
                    ->button('secondary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    protected function showPreview()
    {
        $template = $this->record->template;
        $previewUrl = route('email-template.preview', ['emailTemplate' => $this->record->id]);

        // Return a redirect or modify as needed
        return redirect($previewUrl);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
