<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Models\PostAnalytics;
use App\Repositories\PostCrudRepo;

class PostController extends Controller
{

    protected PostCrudRepo $repo;

    public function __construct(PostCrudRepo $repo)
    {   
        $this->repo = $repo;
    }

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
        $post = $this->repo->create( $request );

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
        $post = $this->repo->read($id);

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
        $post = $this->repo->update( $request->input('data'), $id );

        return (new PostResource($post))
            ->toResponse($request)
            ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $success = $this->repo->delete( $id );

        return response()
            ->json(['data' => ['success' => $success]])
            ->setStatusCode($success ? 200 : 500);
    }

}
