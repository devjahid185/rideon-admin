<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;

class TranslationController extends Controller
{
    private $itemsPerPage = 100;  // Number of translation items per page

    public function listLanguages()
    {
        // Fetch all language directories from the resources/lang path
        $langPath = resource_path('lang');
        $directories = array_filter(glob($langPath.'/*'), 'is_dir');

        $languages = [];
        foreach ($directories as $directory) {
            $languages[] = basename($directory);
        }

        return view('admin.translations.languages', ['languages' => $languages]);
    }

    public function editTranslations($locale, $page = 1)
    {
        // Read the locale file from resources
        $translationPath = resource_path('lang/'.$locale.'/global.php');

        if (! File::exists($translationPath)) {
            return redirect()->back()->with('error', 'Language file not found.');
        }

        $translations = include $translationPath;

        // Create a paginator for the translations
        $paginator = new LengthAwarePaginator(
            array_slice($translations, ($page - 1) * $this->itemsPerPage, $this->itemsPerPage, true),
            count($translations),
            $this->itemsPerPage,
            $page
        );
        $currentPage = $page;
        $totalPages = 30;

        return view('admin.translations.edit', ['translations' => $paginator, 'locale' => $locale, 'currentPage' => $currentPage, 'totalPages' => $totalPages]);
    }

    public function saveTemporaryTranslations(Request $request, $locale)
    {
        $translationPath = resource_path('lang/'.$locale.'/global.php');
        $data = include $translationPath;
        $translations = $request->input('translations');
        foreach ($translations as $key => $value) {

            $data[$key] = $value;
        }

        if (! $translations || ! is_array($translations)) {
            return redirect()->back()->with('error', 'Invalid translation data provided.');
        }

        file_put_contents($translationPath, '<?php return '.var_export($data, true).';');

        return redirect()->back()->with('success', 'Translations saved for review!');
    }

    public function reviewTranslations($locale)
    {
        $tempPath = storage_path('temp_translations/'.$locale.'.php');

        if (! File::exists($tempPath)) {
            return redirect()->back()->with('error', 'Temporary translation file not found.');
        }

        $translations = include $tempPath;

        return view('admin.translations.review', ['translations' => $translations, 'locale' => $locale]);
    }

    public function finalizeTranslations($locale)
    {
        $tempPath = storage_path('temp_translations/'.$locale.'.php');
        $translationPath = resource_path('lang/'.$locale.'.php');

        if (! File::exists($tempPath)) {
            return redirect()->back()->with('error', 'Temporary translation file not found.');
        }

        // This will replace the main translation file with the temp one. Be careful with this step!
        File::copy($tempPath, $translationPath);

        // Optionally, delete the temporary file after the operation
        File::delete($tempPath);

        return redirect()->back()->with('success', 'Translations have been updated successfully!');
    }
}
