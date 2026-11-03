<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\AttendanceLogResource\Pages;
use ImranDevBd\AttendanceHub\Models\AttendanceLog;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceLogResource extends Resource
{
    protected static ?string $model = AttendanceLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'People & HR';
    protected static ?string $navigationLabel = 'Attendance Logs (Hub)';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('attendance_hub');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('device_user_id')
                    ->label('Device User ID')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('punched_at')
                    ->label('Punch Timestamp')
                    ->dateTime('M j, Y H:i:s')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('punch_type')
                    ->colors([
                        'success' => 'check_in',
                        'warning' => 'check_out',
                        'info' => 'break_start',
                        'gray' => 'break_end',
                    ]),
                Tables\Columns\TextColumn::make('verify_mode')
                    ->label('Verification Mode')
                    ->badge(),
                Tables\Columns\TextColumn::make('device_id')
                    ->label('Device Terminal'),
            ])
            ->defaultSort('punched_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceLogs::route('/'),
        ];
    }
}
