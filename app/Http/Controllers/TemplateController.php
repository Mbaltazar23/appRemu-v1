<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemplateFormRequest;
use App\Models\Template;
use App\Models\Tuition;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Template::class, 'template');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $typeItem = $request->get('typeItem', 1); // '1' por defecto para docentes
        $schoolId = auth()->user()->school_id_session; // Obtener el ID del colegio del usuario autenticado
        // Obtener plantillas
        $templates = Template::getTemplate($schoolId, $typeItem);
        // Obtener tuitions
        $tuitions = Tuition::getLiquidationTitlesBySchool($schoolId);

        $typeTitle = Template::getTemplatesTypes()[$typeItem];

        $templates = Template::processTemplates($templates);

        $templateTypes = Template::getTemplatesTypes();

        return view('templates.index', compact('templates', 'typeItem', 'typeTitle', 'tuitions', 'templateTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $template = new Template();
        $typeItem = $request->get('typeItem'); // Tipo de plantilla
        $schoolId = auth()->user()->school_id_session;

        $typeTitle = Template::getTemplatesTypes()[$typeItem];

        $tuitions = Tuition::getLiquidationTitlesBySchool($schoolId);

        $templates = Template::getTemplate($schoolId, $typeItem);

        $templates = Template::processTemplates($templates);

        $lineTypes = Template::getLineTypes();

        return view('templates.create', compact('template', 'templates', 'typeItem', 'typeTitle', 'tuitions', 'lineTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TemplateFormRequest $request)
    {
        // Llamar al método del modelo, pasando el Request completo
        Template::addLine($request->validated());

        $typeItem = $request->input('type'); // Tipo de plantilla
        // Redirigir o devolver respuesta con éxito
        return redirect()->route('templates.index', ['typeItem' => $typeItem])->with('success', 'Línea agregada correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Template $template, Request $request)
    {
        $typeItem = $request->get('typeItem'); // Tipo de plantilla

        $schoolId = auth()->user()->school_id_session;

        $typeTitle = Template::getTemplatesTypes()[$typeItem];

        $tuitions = Tuition::getLiquidationTitlesBySchool($schoolId);

        $templates = Template::getTemplate($schoolId, $typeItem);

        $templates = Template::processTemplates($templates);

        $lineTypes = Template::getLineTypes();
        return view('templates.edit', compact('template', 'templates', 'typeItem', 'typeTitle', 'tuitions', 'lineTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TemplateFormRequest $request, Template $template)
    {
        $typeItem = $request->get('typeItem'); // Tipo de plantilla
        //Codigo del proceso va aqui
        $template->updateLine($request->validated());

        return redirect()->route('templates.index', ['typeItem' => $typeItem])->with('success', 'Línea actualizada correctamente.');
    }

    /**
     * Mueve la plantilla hacia arriba.
     */
    public function moveUp(Template $template, $position)
    {
        // Lógica para mover la plantilla hacia arriba
        if ($position > 1) {
            Template::swapPositions($template->school_id, $template->type, $position, $position - 1);
        }

        return redirect()->route('templates.index', ['typeItem' => $template->type])->with('success', 'Item Subido en la Plantilla Exitosamente !!');
    }

    public function moveDown(Template $template, $position)
    {
        // Lógica para mover la plantilla hacia abajo
        if ($position < Template::where('school_id', $template->school_id)->where('type', $template->type)->count()) {
            Template::swapPositions($template->school_id, $template->type, $position, $position + 1);
        }

        return redirect()->route('templates.index', ['typeItem' => $template->type])->with('success', 'Item Bajado en la Plantilla Exitosamente !!');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Template $template, Request $request)
    {
        $typeItem = $request->get('typeItem'); // Tipo de plantilla

        // Llamar al método que elimina la línea y ajusta las posiciones
        Template::deleteLine($template->school_id, $template->type, $template->position);

        // Redirigir después de la eliminación
        return redirect()->route('templates.index', ['typeItem' => $typeItem])
            ->with('success', 'Item de Plantilla Eliminado correctamente !!');
    }
}
