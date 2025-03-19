<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Category;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Post Information')
                    ->description('Add information about your post here')
                    ->schema([
                            Hidden::make('user_id')
                            ->default(function() {
                            $userId = auth()->user()->id;
                            if (!$userId) {
                                throw new \Exception('No user authenticated');
                            }
                            return $userId;
                            })
                            ->required(),

                            TextInput::make('title')->required(),
                            TextInput::make('slug')->required(),
                            ColorPicker::make('color')->required(),
                            Select::make('category_id')
                                ->label('Category')
                                ->options(Category::all()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),

                            MarkdownEditor::make('content')
                                ->required()
                                ->columnSpanFull(),

                            ])
                            ->columns(2)
                            ->columnSpan(2),
                
                Group::make()
                ->schema([
                    Section::make('Image')
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->disk('public')
                            ->image(),
                    ]),       
                    
                    Section::make('Meta')
                    ->schema([
                            TagsInput::make('tags')->required(),

                            Toggle::make('published')
                                ->onIcon('heroicon-o-check-circle')
                                ->offIcon('heroicon-o-x-circle')
                                ->onColor('success')
                                ->inline(false),
                            
                            ]),
                        ])

            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('user.name'),
                TextColumn::make('slug'),
                ImageColumn::make('thumbnail')
                    ->disk('public')
                    ->circular(),
                ColorColumn::make('color'),
                ToggleColumn::make('published'),
                TextColumn::make('created_at')->dateTime(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
