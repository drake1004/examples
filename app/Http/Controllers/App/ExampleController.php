<?php
namespace example\Http\Controllers\App;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use example\Models\Example;
class EampleController extends AppController
{
    public function add(Request $request)
    {
        $this->validateRequestObject($request, 'example', Example::$rules);
        $site = Example::create($request->input('site'));
        $site->save();
        $reqObj = $request->input('example.targeting.require');
        if (null != $reqObj) {
            foreach (array_keys($reqObj) as $key) {
                $value = $reqObj[$key];
                $site->siteRequires()->create(['key' => $key, 'value' => $value]);
            }
        }
        $reqObj = $request->input('example.targeting.exclude');
        if (null != $reqObj) {
            foreach (array_keys($reqObj) as $key) {
                $value = $reqObj[$key];
                $site->siteExcludes()->create(['key' => $key, 'value' => $value]);
            }
        }
        $response = self::json(compact('example'), 201);
        $response->header('Location', route('app.example.read', ['site' => $site]));
        return $response;
    }
    public function browse(Request $request)
    {

        $examples = Example::with([
            'exampleExcludes' => function ($query) {
                /* @var $query Builder */
                $query->whereNull('deleted_at');
            },
            'exampleRequires' => function ($query) {
                /* @var $query Builder */
                $query->whereNull('deleted_at');
            },
        ])->whereNull('deleted_at')->get();
        return self::json($sites);
    }
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \example\Exceptions\JsonResponseException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function count(Request $request)
    {
        $exampleCount = [
            'exampleEarnings' => 0,
            'exampleClicks' => 0,
            'exampleImpressions' => 0,
        ];
        $response = self::json($exampleCount, 200);
        return $response;
    }
    public function edit(Request $request, $exampleId)
    {
        $this->validateRequestObject($request, 'example', array_intersect_key(Example::$rules, $request->input('example')));
        $example = Example::whereNull('deleted_at')->findOrFail($site_id);
        $example->update($request->input('example'));
        return self::json(['message' => 'Successfully edited'], 200);
    }
    public function delete(Request $request, $exampleId)
    {
        // TODO check privileges
        $example = Example::whereNull('deleted_at')->findOrFail($exampleId);
        $example->deleted_at = new \DateTime();
        $example->save();
        return self::json(['message' => 'Successfully deleted'], 200);
    }
    public function read(Request $request, $exampleId)
    {
        $example = Example::with([
            'exampleExcludes' => function ($query) {
                $query->whereNull('deleted_at');
            },
            'exampleRequires' => function ($query) {
                $query->whereNull('deleted_at');
            },
        ])->whereNull('deleted_at')->findOrFail($site_id);
        return self::json(compact('site'));
    }
}
