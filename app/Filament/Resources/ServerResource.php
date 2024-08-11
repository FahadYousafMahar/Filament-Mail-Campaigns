<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServerResource\Pages;
use App\Filament\Resources\ServerResource\RelationManagers;
use App\Models\Server;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServerResource extends Resource
{
    protected static ?string $model = Server::class;
    public static ?string $label = 'Server';
    public static ?string $pluralLabel = 'Servers';
    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->label('Name'),

            TextInput::make('url')
                ->nullable()
                ->label('URL')
                ->url(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Server Name'),
                Tables\Columns\TextColumn::make('url')->label('Server URL'),
                Tables\Columns\IconColumn::make('emailtemplate_exists')
                    ->label('Has Mail Template ? ')
                    ->exists('emailtemplate')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServers::route('/'),
            'create' => Pages\CreateServer::route('/create'),
            'edit' => Pages\EditServer::route('/{record}/edit'),
        ];
    }
}
