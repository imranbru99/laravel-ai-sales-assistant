<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\LeaveRequestResource\Pages;
use App\Models\LeaveRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-sun';
    protected static ?string $navigationGroup = 'People & HR';
    protected static ?string $navigationLabel = 'Leave & Time-Off';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('leave_management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Leave Application')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'first_name')
                            ->required(),
                        Forms\Components\Select::make('leave_type')
                            ->options([
                                'annual' => 'Annual Paid Leave',
                                'sick' => 'Sick / Medical Leave',
                                'casual' => 'Casual Leave',
                                'maternity' => 'Maternity / Paternity',
                            ])
                            ->default('casual')
                            ->required(),
                        Forms\Components\DatePicker::make('start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->required(),
                        Forms\Components\TextInput::make('total_days')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Forms\Components\Textarea::make('reason')
                            ->columnSpanFull()
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Staff Member')
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('leave_type')
                    ->colors(['primary']),
                Tables\Columns\TextColumn::make('start_date')
                    ->date('M j, Y'),
                Tables\Columns\TextColumn::make('end_date')
                    ->date('M j, Y'),
                Tables\Columns\TextColumn::make('total_days')->label('Days'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn (LeaveRequest $record) => $record->status === 'pending')
                    ->action(function (LeaveRequest $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()->title('Leave request approved.')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveRequests::route('/'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
