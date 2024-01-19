<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    /**
     * @throws Exception
     */
    public function create(Request $request)
    {
        // TODO: move to Request class
        $request->validate([
            'article.title' => 'required|string|max:255|unique:articles,title',
            'article.description' => 'required|string|max:255',
            'article.body' => 'required|string|max:100000',
            'article.tagList' => 'array',
            'article.tagList.*' => 'string',
        ]);

        // TODO: create service and repository layer
        DB::beginTransaction();
        try {
            if ($request->has('article.tagList')) {
                $tagIds = [];
                foreach ($request->input('article.tagList') as $tag) {
                    //TODO: combine to one statement
                    $tag = Tag::firstOrCreate(['tag' => $tag, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
                    $tagIds[] = $tag->id;
                }
            }

            $article = Article::create([
                'title' => $request->input('article.title'),
                'description' => $request->input('article.description'),
                'body' => $request->input('article.body'),
                'user_id' => auth()->user()->id,
            ]);

            $article->tags()->attach($tagIds);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return response()->json([
            "article" => [
                "slug" => implode('-', explode(' ', $article->title)),
                "title" => $article->title,
                "description" => $article->description,
                "body" => $article->body,
                "tagList" => $article->tags->pluck('tag'),
                "createdAt" => $article->created_at,
                "updatedAt" => $article->updated_at,
                // TODO: unimplemented
                "favorited" => false,
                // TODO: unimplemented
                "favoritesCount" => 0,
                "author" => [
                    "username" => auth()->user()->name,
                    "bio" => auth()->user()->bio,
                    "image" => auth()->user()->image,
                    // TODO: unimplemented
                    "following" => false
                ]
            ]
        ]);
    }
}
