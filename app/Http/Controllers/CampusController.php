<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\Campus;

use App\Rules\AlphaSpaces;

class CampusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $campuses = Campus::orderBy( 'name' )->get();

        return view( 'campus.index', compact( 'campuses' ) );
    }

    /**
     * Show the form for adding or updating
     *
     */
    public function form( Campus $campus )
    {
        return view( 'campus.form', compact( 'campus' ) );
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function store( Request $request )
    {
        $validate = $request->validate([
            'name' => [ 'required', 'unique:campuses,name', new AlphaSpaces ],
            'abbreviation' => [ 'required', 'unique:campuses,abbreviation', 'alpha'],
            'code' => [ 'required', 'unique:campuses,code', 'numeric' ]
        ]);

        Campus::insert([$validate]);

        flash( 'Campus has been successfully created' );
        return redirect( '/campuses' );

    }

    /**
     * Display the specified resource.
     *
     */
    public function show(Campus $campus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update( Request $request, Campus $campus )
    {
        $validate = $request->validate([
            'name' => [ 'required', Rule::unique( 'campuses', 'name' )->ignore( $campus->id ) , new AlphaSpaces ],
            'abbreviation' => [ 'required', Rule::unique( 'campuses', 'abbreviation' )->ignore( $campus->id ) , 'alpha'],
            'code' => [ 'required', Rule::unique( 'campuses', 'code' )->ignore( $campus->id ), 'numeric' ]
        ]);

        $campus->fill( $request->all() );
        $campus->save();

        flash( $campus->name . 'has been successfully updated.' );
        return redirect( '/campuses' );
    }

    /**
     *  Remove the specified resource from storage.
     *  AJAX
     */
    public function destroy( Campus $campus )
    {
        $campus->delete();

        return response()->json([
            'campus' => $campus
        ]);
    }
}
