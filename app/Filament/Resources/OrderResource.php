<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Tables\Columns\{TextColumn, BadgeColumn};
use Filament\Forms\Form;
use Filament\Forms\Components\{Select, Repeater, TextInput};
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Vendas';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Pedidos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('client_id')
                    ->relationship('client', 'name')
                    ->required()
                    ->label('Cliente')
                    ->columnSpanFull(),

                Repeater::make('order_items') 
                    ->label('Produtos')
                    ->schema([
                        Select::make('product_id')
                            ->label('Produto')
                            ->options(
                                \App\Models\Product::all()->pluck('name', 'id')
                            )
                            ->required(),

                        TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->label('Quantidade'),

                        TextInput::make('unit_price')
                            ->numeric()
                            ->required()
                            ->label('Preço Unitário'),

                        TextInput::make('total')
                            ->numeric()
                            ->label('Total')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(4)
                    ->defaultItems(1)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')->label('Cliente')->searchable(),
                TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Nº Produtos'),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i'),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
