<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\PayrollResource\Pages;
use App\Models\Payroll;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'People & HR';
    protected static ?string $navigationLabel = 'Payroll & Payslips';
    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('payroll_lite');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Salary & Period')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'first_name')
                            ->required(),
                        Forms\Components\TextInput::make('month')
                            ->numeric()
                            ->default(fn () => (int) date('m'))
                            ->required(),
                        Forms\Components\TextInput::make('year')
                            ->numeric()
                            ->default(fn () => (int) date('Y'))
                            ->required(),
                        Forms\Components\TextInput::make('basic_salary')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\TextInput::make('allowances')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        Forms\Components\TextInput::make('commission_amount')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        Forms\Components\TextInput::make('deductions')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        Forms\Components\TextInput::make('net_salary')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'approved' => 'Approved',
                                'paid' => 'Paid',
                            ])
                            ->default('draft'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Staff Member')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('month')
                    ->formatStateUsing(fn ($record) => date('F', mktime(0, 0, 0, $record->month, 10)) . ' ' . $record->year)
                    ->label('Period'),
                Tables\Columns\TextColumn::make('basic_salary')
                    ->money('USD'),
                Tables\Columns\TextColumn::make('commission_amount')
                    ->money('USD')
                    ->label('Commission'),
                Tables\Columns\TextColumn::make('net_salary')
                    ->money('USD')
                    ->weight('bold')
                    ->color('success'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'info' => 'approved',
                        'success' => 'paid',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('downloadPayslip')
                    ->label('Payslip PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (Payroll $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo $record->getPayslipPdfDocument()->output();
                        }, "Payslip-{$record->employee->employee_id}-{$record->month}-{$record->year}.pdf");
                    }),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayrolls::route('/'),
            'create' => Pages\CreatePayroll::route('/create'),
            'edit' => Pages\EditPayroll::route('/{record}/edit'),
        ];
    }
}
