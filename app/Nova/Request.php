<?php

namespace App\Nova;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;

class Request extends Resource
{
    public static $group = 'Requests';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Request::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'subject';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'subject',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(\Illuminate\Http\Request $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('uuid')->readonly(),

            Text::make('subject')
                ->sortable()
                ->rules('required', 'max:255'),

            Trix::make('body')
                ->sortable()
                ->rules('required'),

            BelongsTo::make('Sent By','user', User::class),

            BelongsTo::make('Category','category',RequestCategory::class),

            DateTime::make('Read At', 'read_at'),

            DateTime::make('Created At', 'created_at')->hideWhenCreating(),

            HasMany::make('Replies','replies',RequestReply::class)
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(\Illuminate\Http\Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(\Illuminate\Http\Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(\Illuminate\Http\Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(\Illuminate\Http\Request $request)
    {
        return [];
    }
}
