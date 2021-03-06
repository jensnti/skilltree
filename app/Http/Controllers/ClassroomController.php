<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Google_Client;
use Google_Service_Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ClassroomController extends Controller
{
    protected $service;
    protected $client;
    protected $optParams;

    public function __construct()
    {
        $this->optParams = [
            'courseStates' => 'ACTIVE',
            'teacherId' => `"me" `
        ];
    }

    public function listCourses()
    {
        $user = Auth::user();
        $this->client = new Google_Client();
        $this->client->setApplicationName('Skilltree');
        $this->client->setAuthConfig('../credentials.json');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        $this->client->setAccessToken($user->g_token);

        $this->service = new Google_Service_Classroom($this->client);

        $this->optParams['fields'] = 'courses(id,descriptionHeading)';

        $coursesResult = Cache::remember('_listCourses_user' . auth()->user()->id, 3600, function () {
            return $this->service->courses->listCourses($this->optParams);
        });


        $dump = [];
        foreach ($coursesResult->getCourses() as $course) {
            array_push($dump, $course);
        }

        if (request()->wantsJson()) {
            return ['message' => $dump];
        }

        return $dump;
    }

    public function listTopics(Request $request)
    {
        $user = Auth::user();
        $this->client = new Google_Client();
        $this->client->setApplicationName('Skilltree');
        $this->client->setAuthConfig('../credentials.json');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        $this->client->setAccessToken($user->g_token);

        $this->service = new Google_Service_Classroom($this->client);

        $topicsResult = Cache::remember('_listCoursesTopics' . $request->courseid . '_user' . auth()->user()->id, 3600, function () use ($request) {
            return $this->service->courses_topics->listCoursesTopics($request->courseid, ['fields' => 'topic(courseId,name,topicId)']);
        });

        //dd($topicsResult->topic);
        if (request()->wantsJson()) {
            return ['message' => $topicsResult->topic];
        }
        return $topicsResult->topic;
    }

    public function listCourseWorks(Request $request)
    {
        $user = Auth::user();
        $this->client = new Google_Client();
        $this->client->setApplicationName('Skilltree');
        $this->client->setAuthConfig('../credentials.json');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        $this->client->setAccessToken($user->g_token);

        $this->service = new Google_Service_Classroom($this->client);

        $coursesResult = Cache::remember('_listCoursesCourseWork' . $request->courseid . '_user ' . auth()->user()->id, 3600, function () use ($request) {
            return $this->service->courses_courseWork->listCoursesCourseWork($request->courseid, ['fields' => 'courseWork(id,courseId,title,topicId)']);
        });

        if (request()->wantsJson()) {
            return ['message' => $coursesResult->courseWork];
        }
        return $coursesResult->courseWork;
    }

    public function getCourseWork(Request $request)
    {
        $user = Auth::user();
        $this->client = new Google_Client();
        $this->client->setApplicationName('Skilltree');
        $this->client->setAuthConfig('../credentials.json');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        $this->client->setAccessToken($user->g_token);

        $this->service = new Google_Service_Classroom($this->client);

        $coursesResult = $this->service->courses_courseWork->listCoursesCourseWork($request->courseid);

        if (request()->wantsJson()) {
            return ['message' => $coursesResult->courseWork];
        }
        return $coursesResult->courseWork;
    }


    public function listCourseStudents(Request $request)
    {
        $user = Auth::user();
        $this->client = new Google_Client();
        $this->client->setApplicationName('Skilltree');
        $this->client->setAuthConfig('../credentials.json');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        $this->client->setAccessToken($user->g_token);

        $this->service = new Google_Service_Classroom($this->client);

        $coursesResult = Cache::remember('_listCoursesStudents' . $request->courseid . '_user ' . auth()->user()->id, 86400, function () use ($request) {
            return $this->service->courses_students->listCoursesStudents($request->courseid, ['fields' => 'students(profile/emailAddress)']);
        });

        if (request()->wantsJson()) {
            return ['message' => $coursesResult->students];
        }
        return $coursesResult->students;
    }
}
