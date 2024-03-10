<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                       ->description()
                       ->schema([
                           TextInput::make('name'),
                           TextInput::make('email')->email(),
                           TextInput::make('password')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create'),
                           Select::make('roles')->multiple()->relationship('roles', 'name'),
                       ]),

            ]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('created_at')->dateTime(),
                TextColumn::make('roles.name')->listWithLineBreaks(),
            ])
            ->filters([
                Filter::make('email_verified_at')->label('Email verified')->query(function (Builder $query): Builder {
                    return $query->whereNotNull('email_verified_at');
                }),
                Filter::make('created_at')
                      ->form([
                          DatePicker::make('created_from'),
                          DatePicker::make('created_until'),
                      ])
                      ->query(function (Builder $query, array $data): Builder {
                          return $query
                              ->when(
                                  $data['created_from'],
                                  fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                              )
                              ->when(
                                  $data['created_until'],
                                  fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                              );
                      }),
                TrashedFilter::make('trashed'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
