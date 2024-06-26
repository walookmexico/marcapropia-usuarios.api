<?php

namespace App\Http\Controllers;

class ProjectController extends Controller
{

    public function show($id)
    {
        return '{"nodes":[{"id":"3","type":"input","position":{"x":100,"y":0},"data":{"label":"Inicio"},"modelData":{"status":2},"style":{"width":50,"height":50,"display":"flex","alignItems":"center","justifyContent":"center"},"width":50,"height":50,"selected":false,"positionAbsolute":{"x":100,"y":0}},{"id":"4","type":"output","position":{"x":105.63612884257404,"y":281.43610696074404},"data":{"label":"Fin"},"modelData":{"status":2},"style":{"width":50,"height":50,"display":"flex","alignItems":"center","justifyContent":"center"},"width":50,"height":50,"selected":false,"positionAbsolute":{"x":105.63612884257404,"y":281.43610696074404},"dragging":false},{"id":"5","type":"textUpdater","position":{"x":145.75,"y":62.5},"data":{"id":"5","creacion":"2024-06-10T02:59:03.594Z","modelInputs":{"title":"","document":false,"dueDate":false},"documentos":[],"formularios":[]},"modelData":{"status":0},"style":{"backgroundColor":"#F2F0F3"},"selected":false,"positionAbsolute":{"x":145.75,"y":62.5}},{"id":"6","type":"default","position":{"x":59.428724345782626,"y":92.0839674825322},"data":{"label":"default node","documentos":[],"formularios":[]},"modelData":{"status":0},"style":{"backgroundColor":"#F2F0F3"},"width":150,"height":39,"selected":false,"positionAbsolute":{"x":59.428724345782626,"y":92.0839674825322},"dragging":false},{"id":"7","type":"default","position":{"x":89.2278415533909,"y":197.20863096492778},"data":{"label":"default node","documentos":[],"formularios":[]},"modelData":{"status":0},"style":{"backgroundColor":"#F2F0F3"},"width":150,"height":39,"selected":false,"positionAbsolute":{"x":89.2278415533909,"y":197.20863096492778},"dragging":false}],"edges":[{"id":"e3-6","source":"3","target":"6","type":"smoothstep","markerEnd":{"type":"arrowclosed"}},{"id":"e6-7","source":"6","target":"7","type":"smoothstep","markerEnd":{"type":"arrowclosed"}},{"id":"e7-4","source":"7","target":"4","type":"smoothstep","markerEnd":{"type":"arrowclosed"}}],"viewport":{"x":34.20478333156984,"y":60.75432764414979,"zoom":1.2080894796040675}}';
    }
}
