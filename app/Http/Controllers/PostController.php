<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PostResource;
use App\Models\PostAnalytics;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();

        $data = $request->get('data');
        $data['user_id'] = $user->id;

        $post = Post::create( $data );

        if($data['analytics']){
            PostAnalytics::find( $post->id )
                ->update($data['analytics']);
        }

        if($request->has('with')){
            $post->load(array_intersect(
                $request->get('with'),
                Post::getRelations(),
            ));
        }

        return (new PostResource($post))
            ->toResponse($request)
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        $relations = array_intersect(
            $request->get('with', []),
            Post::getRelations(),
        );

        $post = Post::with( $relations )
            ->findOrFail( $id );
        
        return (new PostResource($post))
            ->toResponse($request)
            ->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $relations = array_intersect(
            $request->get('with', []),
            Post::getRelations(),
        );

        $post = Post::with( $relations )->findOrFail( $id );

        $data = $request->get('data', []);

        $post->update( array_intersect_key( Post::getFillable(), $data) );
        
        // update relations if set?
        if($data['analytics']){
            PostAnalytics::findOrFail( $post->id )
                ->update(array_intersect_key(
                    PostAnalytics::getFillable(),
                    $data['analytics']
                ));
        }

        return (new PostResource($post))
            ->toResponse($request)
            ->setStatusCode(201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Post::findOrFail( $id )->delete();

        return response()
            ->json(['success' => true])
            ->setStatusCode(200);
    }

}
