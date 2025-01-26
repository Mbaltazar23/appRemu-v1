<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemplateFormRequest;
use App\Models\Template;
use App\Models\Tuition;
use Illuminate\Http\Request;

class TemplateController extends Controller {

    // Constructor to authorize resource actions
    public function __construct() {
        $this->authorizeResource(Template::class, 'template');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $typeItem = $request->get('typeItem', 1); // '1' by default for teachers
        // Get the school ID of the authenticated user
        $schoolId = auth()->user()->school_id_session;
        // Retrieve templates
        $templates = Template::getTemplate($schoolId, $typeItem);
        // Retrieve tuitions (liquidation titles) for the school
        $tuitions = Tuition::getLiquidationTitlesBySchool($schoolId);

        $typeTitle = Template::getTemplatesTypes()[$typeItem];
        // Process the retrieved templates (perhaps for additional formatting or processing)
        $templates = Template::processTemplates($templates);
        // Get the different template types (e.g., teacher, student)
        $templateTypes = Template::getTemplatesTypes();
        // Return the view with the necessary data
        return view('templates.index', compact('templates', 'typeItem', 'typeTitle', 'tuitions', 'templateTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request) {
        $template = new Template(); // Create a new Template object
        $typeItem = $request->get('typeItem'); // Template type
        $schoolId = auth()->user()->school_id_session;

        $typeTitle = Template::getTemplatesTypes()[$typeItem];
        // Retrieve tuitions for the school
        $tuitions = Tuition::getLiquidationTitlesBySchool($schoolId);
        // Get the templates for the school and type
        $templates = Template::getTemplate($schoolId, $typeItem);
        // Process the retrieved templates
        $templates = Template::processTemplates($templates);
        // Get the line types (perhaps for different template lines)
        $lineTypes = Template::getLineTypes();

        // Return the view to create a template
        return view('templates.create', compact('template', 'templates', 'typeItem', 'typeTitle', 'tuitions', 'lineTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TemplateFormRequest $request) {
        // Call the model method to add a line, passing the validated request data
        Template::addLine($request->validated());
        // Template type
        $typeItem = $request->input('type');
        // Redirect to the templates index page with success message
        return redirect()->route('templates.index', ['typeItem' => $typeItem])->with('success', 'Línea agregada correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Template $template, Request $request) {
        $typeItem = $request->get('typeItem'); // Template type

        $schoolId = auth()->user()->school_id_session;

        $typeTitle = Template::getTemplatesTypes()[$typeItem];

        // Retrieve tuitions for the school
        $tuitions = Tuition::getLiquidationTitlesBySchool($schoolId);

        // Get the templates for the school and type
        $templates = Template::getTemplate($schoolId, $typeItem);

        // Process the retrieved templates
        $templates = Template::processTemplates($templates);

        // Get the line types
        $lineTypes = Template::getLineTypes();

        // Return the view to edit the template
        return view('templates.edit', compact('template', 'templates', 'typeItem', 'typeTitle', 'tuitions', 'lineTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TemplateFormRequest $request, Template $template) {
        // Template type
        $typeItem = $request->get('typeItem');
        // Update the line with validated data
        $template->updateLine($request->validated());

        // Redirect to the templates index page with success message
        return redirect()->route('templates.index', ['typeItem' => $typeItem])->with('success', 'Línea actualizada correctamente.');
    }

    /**
     * Move the template up in the list.
     */
    public function moveUp(Template $template, $position) {
        // Logic to move the template up in position if it's not the first item
        if ($position > 1) {
            Template::swapPositions($template->school_id, $template->type, $position, $position - 1);
        }

        // Redirect to the templates index page with success message
        return redirect()->route('templates.index', ['typeItem' => $template->type])->with('success', 'Item Subido Exitosamente !!');
    }

    /**
     * Move the template down in the list.
     */
    public function moveDown(Template $template, $position) {
        // Logic to move the template down in position if it's not the last item
        if ($position < Template::where('school_id', $template->school_id)->where('type', $template->type)->count()) {
            Template::swapPositions($template->school_id, $template->type, $position, $position + 1);
        }
        // Redirect to the templates index page with success message
        return redirect()->route('templates.index', ['typeItem' => $template->type])->with('success', 'Item Bajado Exitosamente !!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Template $template, Request $request) {
        $typeItem = $request->get('typeItem'); // Template type
        // Call the model method to delete the line and adjust the positions
        Template::deleteLine($template->school_id, $template->type, $template->position);

        // Redirect after deletion with success message
        return redirect()->route('templates.index', ['typeItem' => $typeItem])
                        ->with('success', 'Item de Plantilla Eliminado correctamente !!');
    }

}
