<?php

namespace App\Filament\App\Resources;

use App\Core\Modules\ModuleManager;
use App\Filament\App\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'People & HR';
    protected static ?string $navigationLabel = 'Employees Directory';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return app(ModuleManager::class)->isModuleEnabled('employees_directory');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Employee Information')
                    ->schema([
                        Forms\Components\TextInput::make('employee_id')
                            ->required()
                            ->default(fn () => 'EMP-' . rand(100, 999)),
                        Forms\Components\TextInput::make('first_name')
                            ->required(),
                        Forms\Components\TextInput::make('last_name'),
                        Forms\Components\TextInput::make('email')
                            ->email(),
                        Forms\Components\TextInput::make('phone')
                            ->tel(),
                        Forms\Components\TextInput::make('designation')
                            ->required(),
                        Forms\Components\Select::make('department')
                            ->options([
                                'Sales' => 'Sales & Business Development',
                                'Marketing' => 'Marketing',
                                'Support' => 'Customer Success & Support',
                                'Finance' => 'Finance & Accounting',
                                'Operations' => 'Operations & Logistics',
                                'HR' => 'Human Resources',
                            ])
                            ->default('Sales')
                            ->required(),
                        Forms\Components\TextInput::make('salary')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\DatePicker::make('join_date')
                            ->default(now()),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_id')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('designation')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('salary')
                    ->money('USD'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
