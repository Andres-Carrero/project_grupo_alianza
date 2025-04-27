<?php

namespace App\Http\Controllers;

use App\Models\Cocktails;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CocktailsController extends Controller
{

    public function saved()
    {
        $cocktails = Cocktails::with('user')->get();

        return view('cocktails.saved', compact('cocktails'));
    }


    public function index(Request $request)
    {
        try {
            $letter = $request->get('letter', 'A');
            $response = Http::timeout(30)->get('https://www.thecocktaildb.com/api/json/v1/1/search.php?f=' . $letter);

            $cocktails = [];

            if ($response->successful()) {
                $cocktails = $response->json()['drinks'] ?? [];

                $savedDrinks = Cocktails::pluck('drink_id')->toArray(); // trae IDs guardados

                // Recorremos para marcar cuáles están guardados
                foreach ($cocktails as &$cocktail) {
                    $cocktail['is_saved'] = in_array($cocktail['idDrink'], $savedDrinks);
                }
            }

            return view('cocktails.index', compact('cocktails', 'letter'));
        } catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "code" => $e->getCode(),
            ], 400);
        }
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|string|max:255', // ID del drink de la API
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'image_url' => 'required|url',
            ]);

            $exists = Cocktails::where('drink_id', $request->id)->first();

            if ($exists) {
                throw new Exception("Este cóctel ya fue guardado previamente.", 400);
            }

            $cocktail = Cocktails::create([
                'drink_id' => $request->id,
                'user_id' => auth()->id(),
                'name' => $request->name,
                'description' => $request->description,
                'image_url' => $request->image_url,
            ]);

            return response()->json([
                'msg' => 'Cóctel guardado correctamente',
                'data' => $cocktail
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 400);
        }
    }


    public function show($id)
    {
        try {
            $cocktail = Cocktails::findOrFail($id);
            return view('cocktails.show', compact('cocktail'));
        } catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "code" => $e->getCode(),
            ], 400);
        }
    }

    public function edit($id)
    {
        try {
            $cocktail = Cocktails::findOrFail($id);
            return view('cocktails.edit', compact('cocktail'));
        } catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "code" => $e->getCode(),
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $cocktail = Cocktails::findOrFail($id);

            // Actualizar los datos
            $cocktail->name = $request->name;
            $cocktail->description = $request->description;
            $cocktail->save();

            return response()->json([
                'msg' => 'Cóctel actualizado correctamente.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'msg' => 'Error al actualizar el cóctel.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $cocktail = Cocktails::findOrFail($id);
            $cocktail->delete();

            // Redirigir o devolver los cócteles actualizados
            $cocktails = Cocktails::all(); // Obtener todos los cócteles restantes
            return response()->json([
                'msg' => 'Cóctel eliminado correctamente.',
                'cocktails' => $cocktails
            ]);
        } catch (Exception $e) {
            return response()->json([
                'msg' => 'Error al eliminar el cóctel.',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
