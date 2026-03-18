<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Enregistrer une nouvelle catégorie
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Le slug est généré automatiquement par le modèle si Spatie Sluggable est configuré
        $category = Categorie::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Catégorie créée avec succès',
            'data' => $category
        ], 201);
    }

    /**
     * Modifier une catégorie existante
     */
    public function update(Request $request, $id)
    {
        $category = Categorie::find($id);

        if (!$category) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Catégorie mise à jour',
            'data' => $category
        ], 200);
    }

    /**
     * Supprimer une catégorie
     */
    public function destroy($id)
    {
        $category = Categorie::find($id);

        if (!$category) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Catégorie supprimée avec succès'
        ], 200);
    }

   
       public function index()
      {
    return response()->json(Categorie::all(), 200);
     }
      }