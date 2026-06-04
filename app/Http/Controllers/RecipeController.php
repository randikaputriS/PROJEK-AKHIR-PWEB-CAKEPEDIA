<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RecipeController extends Controller
{

    public function index(Request $request): View
    {
        $query = Recipe::query()->latest();

        // ── Filter kategori ────────────────────────────────────────
        $activeCategory = $request->get('category');
        if ($activeCategory && in_array($activeCategory, Recipe::$categories)) {
            $query->where('category', $activeCategory);
        }

        // ── Search berdasarkan judul resep ─────────────────────────
        $searchTerm = $request->get('search');
        if ($searchTerm) {
            $query->where('title', 'LIKE', "%{$searchTerm}%");
        }

        $recipes    = $query->paginate(9)->withQueryString();
        $categories = Recipe::$categories;

        // Hitung jumlah resep per kategori untuk badge counter
        $categoryCounts = Recipe::query()
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('recipes.index', compact(
            'recipes',
            'categories',
            'activeCategory',
            'searchTerm',
            'categoryCounts'
        ));
    }

    /**
     * GET /recipes/{recipe}
     */
    public function show(Recipe $recipe): View
    {
        // Resep lain dalam kategori yang sama (sidebar "Lihat Juga")
        $related = Recipe::where('category', $recipe->category)
            ->where('id', '!=', $recipe->id)
            ->latest()
            ->take(3)
            ->get();

        return view('recipes.show', compact('recipe', 'related'));
    }

    /**
     * GET /recipes/create
     */
    public function create(): View
    {
        $categories = Recipe::$categories;
        return view('recipes.create', compact('categories'));
    }

    /**
     * POST /recipes
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|in:Pastry,Cookies,Traditional Bites',
            'ingredients'  => 'required|string',
            'instructions' => 'required|string',
            'image_url'    => 'nullable|url|max:500',
        ], $this->messages());

        $recipe = Recipe::create($validated);

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('success', "Resep \"{$recipe->title}\" berhasil ditambahkan! 🎂");
    }

    /**
     * GET /recipes/{recipe}/edit
     */
    public function edit(Recipe $recipe): View
    {
        $categories = Recipe::$categories;
        return view('recipes.edit', compact('recipe', 'categories'));
    }

    /**
     * PUT /recipes/{recipe}
     */
    public function update(Request $request, Recipe $recipe): RedirectResponse
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|in:Pastry,Cookies,Traditional Bites',
            'ingredients'  => 'required|string',
            'instructions' => 'required|string',
            'image_url'    => 'nullable|url|max:500',
        ], $this->messages());

        $recipe->update($validated);

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('success', "Resep \"{$recipe->title}\" berhasil diperbarui! ✨");
    }

    /**
     * DELETE /recipes/{recipe}
     */
    public function destroy(Recipe $recipe): RedirectResponse
    {
        $title = $recipe->title;
        $recipe->delete();

        return redirect()
            ->route('recipes.index')
            ->with('success', "Resep \"{$title}\" berhasil dihapus.");
    }

    // ── Helper ─────────────────────────────────────────────────────

    private function messages(): array
    {
        return [
            'title.required'        => 'Judul resep wajib diisi.',
            'category.required'     => 'Kategori wajib dipilih.',
            'category.in'           => 'Kategori tidak valid.',
            'ingredients.required'  => 'Bahan-bahan wajib diisi.',
            'instructions.required' => 'Cara membuat wajib diisi.',
            'image_url.url'         => 'Format URL gambar tidak valid.',
        ];
    }
}