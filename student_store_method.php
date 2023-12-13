
public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:512|dimensions:max_width=300,max_height=300',
        ]);

        if ($validator->fails()) {
            $failedRules = $validator->failed();
            if (isset($failedRules['image']['Required'])) {
                return 1;
            }

            if (isset($failedRules['image']['Mimes'])) {
                return 2;
            }

            if (isset($failedRules['image']['Uploaded'])) {
                return 3;
            }

            if (isset($failedRules['image']['Dimensions'])) {
                return 4;
            }
            return 5;
        }

        if (empty($request->father_last_name)) $father_last_name = '';
        else $father_last_name = $request->father_last_name;

        if (empty($request->guardian_last_name)) $guardian_last_name = '';
        else $guardian_last_name = $request->guardian_last_name;

        if (empty($request->mother_last_name)) $mother_last_name = '';
        else $mother_last_name = $request->mother_last_name;

        $permanent_division = DB::table('divisions')->where('id', $request->get('permanent_division'))->select('name_eng')->first();
        $permanent_division = $permanent_division->name_eng;

        $permanent_district = DB::table('districts')->where('id', $request->get('permanent_district'))->select('name_eng')->first();
        $permanent_district = $permanent_district->name_eng;

        $permanent_upazila = DB::table('upazilas')->where('id', $request->get('permanent_upazila'))->select('name_eng')->first();
        $permanent_upazila = $permanent_upazila->name_eng;

        $present_division = DB::table('divisions')->where('id', $request->get('present_division'))->select('name_eng')->first();
        $present_division = $present_division->name_eng;

        $present_district = DB::table('districts')->where('id', $request->get('present_district'))->select('name_eng')->first();
        $present_district = $present_district->name_eng;

        $present_upazila = DB::table('upazilas')->where('id', $request->get('present_upazila'))->select('name_eng')->first();
        $present_upazila = $present_upazila->name_eng;

        $board_roll = $request->get('board_roll');
        $reg_no = $request->get('reg_no');
        $password = $request->get('password');
        $contact_no = $request->get('contact');
        $student_id = $request->get('student_id');
        $program_id = $request->get('program_id');
        $program_type = $request->get('program_type');

        $level_name = DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('levels')->select('name','value')->where('id', $request->get('level_id'))->first();

        $section_name = DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('sections')->select('name','short_name')->where('id', $request->get('section_id'))->first();

        $get_division = DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('divisions')->select('name','value','roll_start')->where('id', $request->get('division_id'))->first();

        if ($get_division) {
            $division_name = $get_division->name;
        } else {
            $division_name = null;
        }
        //dd($division_name);

        //By Munir
        // $rolls = DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('student_sessions')->where('session_id', $request->get('session_id'))->where('division_id', $request->get('division_id'))->where('section_id', $request->get('section_id'))->latest('id')->select('roll')->first();
        //->latest('id')
        //$rolls->roll;
        //$roll = 1;
        //By Togor
        $rolls = DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('student_sessions')->where('session_id', $request->get('session_id'))->where('division_id', $request->get('division_id'))->where('section_id', $request->get('section_id'))->max('roll');
        
        if ($rolls)
            $roll = (int)$rolls+1;
        else
            $roll = $get_division->roll_start;
        //dd($rolls);
        // if (!empty($rolls))
        //     $roll = (int)$rolls->roll+1;
        // else
        //     $roll = 1;
        //dd($roll);
        // dd($request->get('student_id'));
        // exit();
        
        $current_session = DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('sessions')->where('active', 1)->where('admission', 1)->orderBy('id','DESC')->first();
        $request->merge(['roll'=>$roll]);
        //By Togor 
         $request->merge(['student_id' => $request->get('student_id')]);
         $request->merge(['parent_id' => $request->get('student_id')]);
        
        // $request->merge(['student_id' => ((int)date("y")-1).date('y').$get_division->value.$roll]);
        // $request->merge(['parent_id' => ((int)date("y")-1).date('y').$get_division->value.$roll]);
        //By Munir
        

        $roll_msg = ' Roll : ' . $request->get('roll');
        $division_msg = ', Division : ' . $division_name;
        $msg = 'Admission for ' . $request->get('student_name') .' is confirmed. ' . (($request->get('roll') !== null) ? $roll_msg : '') . ' ' . (($division_name !== null) ? $division_msg : '');
     
        $exist = DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('students')->where('student_id', $request->get('student_id'))->where('active','!=',5)->first();
        
        if ($exist) {
            
            $rolls = DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('student_sessions')->where('session_id', $request->get('session_id'))->where('division_id', $request->get('division_id'))->where('section_id', $request->get('section_id'))->max('roll');
                if ($rolls)
                    $roll = (int)$rolls+1;
                else
                    $roll = $get_division->roll_start;
                    
                    $current_session = DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('sessions')->where('active', 1)->where('admission', 1)->orderBy('id','DESC')->first();
                    
                    $request->merge(['student_id' => ((int)date('y',strtotime($current_session->start))).((int)date('y',strtotime($current_session->end))).$get_division->value.$roll]);
                    $request->merge(['parent_id' => ((int)date('y',strtotime($current_session->start))).((int)date('y',strtotime($current_session->end))).$get_division->value.$roll]);
                    //By Munir
                    
                    // $request->merge(['roll'=>$roll]);
                    // //By Togor 
                    //  $request->merge(['student_id' => $request->get('student_id')]);
                    //  $request->merge(['parent_id' => $request->get('student_id')]);
            
            // return 'studentexist';
        } else {
            $rollExist = DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('student_sessions')->where('session_id', $request->get('session_id'))->where('section_id', $request->get('section_id'))->where('active','!=',5)->where('division_id', $request->get('division_id'))->where('roll', $request->get('roll'))->first();
            if ($rollExist) {
                return 'rollexist';
            } else {
                if ($request->get('parentId')) { //echo 's';return $request->all();
                    if ($request->get('division_id') == 0) {
                        $division_id = null;
                    } else {
                        $division_id = $request->get('division_id');
                    }
                    if ($request->hasFile('image')) {
                        $validator = Validator::make($request->all(), [
                            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                        ]);
                        if ($validator->fails()) {
                            return 'wrongimage';
                        }
                        $image = $request->image;
                        //$input['imagename'] = time() . '.' . $image->getClientOriginalExtension();

                        //$input['imagename'] = date('Y').'-'.$request->get('division_id').'-'.$request->get('student_id').'.' . $image->getClientOriginalExtension();
                        //By Munir
                        $input['imagename'] = date('Y').'-'.$request->get('division_id').'-'.$request->get('student_id').'.' . $image->getClientOriginalExtension();

                        $destinationPath = public_path('images/schools/' . $request->session()->get("tenant")->slug . '/students');
                        $image->move($destinationPath, $input['imagename']);
                    } else {
                        if($request->gender == 'Male'){
                                $input['imagename'] = 'user.png';
                            }else{
                                $input['imagename'] = 'user_female.png';
                            }
                    }
                    
                    $studentId = DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('students')->insertGetId([
                        'student_id' => $request->get('student_id'),
                        'device_id' => $request->get('device_id'),
                        'password' => Hash::make($request->get('student_id')),
                        'student_name' => $request->get('student_name'),
                        'birth_reg' => $request->get('birth_reg'),
                        //'last_name' => $last_name,
                        'dob' => date('Y-m-d',strtotime($request->get('dob'))),
                        'gender' => $request->get('gender'),
                        'religion' => $request->get('religion'),
                        'blood_group' => $request->get('blood_group'),
                        'contact' => $request->get('contact'),
                        'email' => $request->get('email'),
                        'nationality' => $request->get('nationality'),
                        'nid' => $request->get('nid'),
                        'permanent_address' => $request->get('permanent_address'),
                        'permanent_village' => $request->get('permanent_village'),
                        'permanent_division' => $permanent_division,
                        'permanent_district' => $permanent_district,
                        'permanent_upazila' => $permanent_upazila,
                        'permanent_postal' => $request->get('permanent_postal'),
                        'present_address' => $request->get('present_address'),
                        'present_village' => $request->get('present_village'),
                        'present_division' => $present_division,
                        'present_district' => $present_district,
                        'present_upazila' => $present_upazila,
                        'present_postal' => $request->get('present_postal'),
                        'admission_date' => $request->get('admission_date'),
                        'extra_qualification' => $request->get('extra_qualification'),
                        'previous_school' => $request->get('previous_school'),
                        'previous_level' => $request->get('previous_level'),
                        'guardian_first_name' => $request->get('guardian_first_name'),
                        'guardian_nid' => $request->get('guardian_nid'),
                        'guardian_last_name' => $guardian_last_name,
                        'guardian_contact' => $request->get('guardian_contact'),
                        'image' => $input['imagename'],
                        'active' => $request->get('active'),
                        'remarks' => $request->get('remarks'),
                        'gpa_without_4th' => $request->get('gpa_without_4th'),
                        'ssc_board' => $request->get('ssc_board'),
                        'ssc_year' => $request->get('ssc_year'),
                        'quota' => $request->get('quota'),
                        'marital_status' => $request->get('marital_status'),
                        'guardian_relation' => $request->get('guardian_relation'),
                        'guardian_income' => $request->get('guardian_income'),
                        'guardian_designation' => $request->get('guardian_designation'),
                        'ssc_gpa' => $request->get('ssc_gpa'),
                        'ssc_roll' => $request->get('board_roll'),
                        'ssc_reg' => $request->get('reg_no'),
                        'hsc_roll' => $request->get('hsc_roll'),
                        'hsc_reg' => $request->get('hsc_reg'),
                        'hsc_board' => $request->get('hsc_board'),
                        'hsc_gpa' => $request->get('hsc_gpa'),
                        'hsc_year' => $request->get('hsc_year'),
                        'hsc_institute_name' => $request->get('hsc_institute_name'),
                        'created_at' => Carbon::now()
                    ]);
                    DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('student_parents')->insert([
                        'student_id' => $studentId,
                        'parent_id' => $request->get('parentId'),
                        'created_at' => Carbon::now()
                    ]);
                    DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('student_sessions')->insert([
                        'student_id' => $studentId,
                        'session_id' => $request->get('session_id'),
                        'board_session_id' => $request->get('session_id'),
                        'section_id' => $request->get('section_id'),
                        'division_id' => $division_id,
                        'roll' => $request->get('roll'),
                        'created_at' => Carbon::now()
                    ]);
                    $subjects = DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('subjects')
                        ->leftJoin('levels', 'levels.id', '=', 'subjects.level_id')
                        ->leftJoin('divisions', 'divisions.id', '=', 'subjects.division_id')
                        ->leftJoin('subject_categories', 'subject_categories.id', '=', 'subjects.category_id')
                        ->where('level_id', $request->get('level_id'))
                        ->where(function ($query) use ($division_id) {
                            $query->where('subjects.division_id', null)
                                ->orWhere('subjects.division_id', $division_id);
                        })
                        ->select('subjects.*', 'levels.name as level_name', 'divisions.name as division_name', 'subject_categories.name as category_name')
                        ->get();
                    $student['id'] = $studentId;
                    $student['session_id'] = $request->get('session_id');
                    $student['subjects'] = json_decode(json_encode($subjects));

                    return view('addSubject', compact('student', 'division_id', 'board_roll', 'reg_no', 'password','program_id','program_type'));
                } else {
                    
                    // echo 'mm';return $request->all();
                    $parentExist = DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('parents')->where('parent_id', $request->get('parent_id'))->where('active', 1)->first();
                    // dd($request->get('parentId'));
                    // dd($parentExist);
                    if ($parentExist) {
                        return 'parentexist';
                    } else {
                        if ($request->get('division_id') == 0) {
                            $division_id = null;
                        } else {
                            $division_id = $request->get('division_id');
                        }
                        if ($request->hasFile('image')) {
                            $validator = Validator::make($request->all(), [
                                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                            ]);
                            if ($validator->fails()) {
                                return 4;
                            }
                            $image = $request->image;

                            //$input['imagename'] = date('Y').'-'.$request->get('division_id').'-'.$request->get('student_id').'.' . $image->getClientOriginalExtension();
                            ////$input['imagename'] = time() . '.' . $image->getClientOriginalExtension();
                            //By Munir
                            $input['imagename'] = date('Y').'-'.$request->get('division_id').'-'.$request->get('student_id').'.' . $image->getClientOriginalExtension();

                            $destinationPath = public_path('images/schools/' . $request->session()->get("tenant")->slug . '/students');
                            $image->move($destinationPath, $input['imagename']);
                        } else {
                            if($request->gender != 'Male'){
                                $input['imagename'] = 'user.png';
                            }else{
                                $input['imagename'] = 'user_female.png';
                            }
                        }
                        $studentId = DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('students')->insertGetId([
                            'student_id' => $request->get('student_id'),
                            'device_id' => $request->get('device_id'),
                            'password' => Hash::make($request->get('student_id')),
                            'student_name' => $request->get('student_name'),
                            'birth_reg' => $request->get('birth_reg'),
                            //'last_name' => $last_name,
                            'dob' => date('Y-m-d',strtotime($request->get('dob'))),
                            'gender' => $request->get('gender'),
                            'religion' => $request->get('religion'),
                            'blood_group' => $request->get('blood_group'),
                            'contact' => $request->get('contact'),
                            'email' => $request->get('email'),
                            'nationality' => $request->get('nationality'),
                            'nid' => $request->get('nid'),
                            'permanent_address' => $request->get('permanent_address'),
                            'permanent_village' => $request->get('permanent_village'),
                            'permanent_division' => $permanent_division,
                            'permanent_district' => $permanent_district,
                            'permanent_upazila' => $permanent_upazila,
                            'permanent_postal' => $request->get('permanent_postal'),
                            'present_address' => $request->get('present_address'),
                            'present_village' => $request->get('present_village'),
                            'present_division' => $present_division,
                            'present_district' => $present_district,
                            'present_upazila' => $present_upazila,
                            'present_postal' => $request->get('present_postal'),
                            'admission_date' => $request->get('admission_date'),
                            'extra_qualification' => $request->get('extra_qualification'),
                            'previous_school' => $request->get('previous_school'),
                            'previous_level' => $request->get('previous_level'),
                            'guardian_first_name' => $request->get('guardian_first_name'),
                            'guardian_nid' => $request->get('guardian_nid'),
                            'guardian_last_name' => $guardian_last_name,
                            'guardian_contact' => $request->get('guardian_contact'),
                            'image' => $input['imagename'],
                            'active' => $request->get('active'),
                            'remarks' => $request->get('remarks'),
                            'gpa_without_4th' => $request->get('gpa_without_4th'),
                            'ssc_board' => $request->get('ssc_board'),
                            'ssc_year' => $request->get('ssc_year'),
                            'quota' => $request->get('quota'),
                            'marital_status' => $request->get('marital_status'),
                            'guardian_relation' => $request->get('guardian_relation'),
                            'guardian_income' => $request->get('guardian_income'),
                            'guardian_designation' => $request->get('guardian_designation'),
                            'ssc_gpa' => $request->get('ssc_gpa'),
                            'ssc_roll' => $request->get('board_roll'),
                            'ssc_reg' => $request->get('reg_no'),
                            'hsc_roll' => $request->get('hsc_roll'),
                            'hsc_reg' => $request->get('hsc_reg'),
                            'hsc_board' => $request->get('hsc_board'),
                            'hsc_gpa' => $request->get('hsc_gpa'),
                            'hsc_year' => $request->get('hsc_year'),
                            'hsc_institute_name' => $request->get('hsc_institute_name'),
                            'created_at' => Carbon::now()
                        ]);
                        $parentID = DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('parents')->insertGetId([
                            'parent_id' => $request->get('parent_id'),
                            'password' => Hash::make($request->get('parent_password')),
                            'father_first_name' => $request->get('father_first_name'),
                            'father_last_name' => $father_last_name,
                            'father_nid' => $request->get('father_nid'),
                            'father_ocupation' => $request->get('father_ocupation'),
                            'father_income' => $request->get('father_income'),
                            'father_contact' => $request->get('father_contact'),
                            'mother_first_name' => $request->get('mother_first_name'),
                            'mother_last_name' => $mother_last_name,
                            'mother_contact' => $request->get('mother_contact'),
                            'created_at' => Carbon::now()
                        ]);
                        DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('student_parents')->insert([
                            'student_id' => $studentId,
                            'parent_id' => $parentID,
                            'created_at' => Carbon::now()
                        ]);
                        DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('student_sessions')->insert([
                            'student_id' => $studentId,
                            'session_id' => $request->get('session_id'),
                            'section_id' => $request->get('section_id'),
                            'board_session_id' => $request->get('session_id'),
                            'division_id' => $division_id,
                            'roll' => $request->get('roll'),
                            'created_at' => Carbon::now()
                        ]);

                        $subjects = DB::connection(config('values.db_prefix') . $request->session()->get('tenant')->slug . '_db')->table('subjects')
                            ->leftJoin('levels', 'levels.id', '=', 'subjects.level_id')
                            ->leftJoin('divisions', 'divisions.id', '=', 'subjects.division_id')
                            ->leftJoin('subject_categories', 'subject_categories.id', '=', 'subjects.category_id')
                            ->where('level_id', $request->get('level_id'))
                            ->where('subjects.active',1)
                            ->where(function ($query) use ($division_id) {
                                $query->where('subjects.division_id', null)
                                    ->orWhere('subjects.division_id', $division_id);
                            })
                            ->select('subjects.*', 'levels.name as level_name', 'divisions.name as division_name', 'subject_categories.name as category_name')
                            ->get();
                        $student['id'] = $studentId;
                        $student['session_id'] = $request->get('session_id');
                        $student['subjects'] = $subjects;

                        return view('addSubject', compact('student', 'board_roll', 'reg_no', 'password', 'division_id', 'contact_no', 'student_id','program_id','program_type'));
                        //return view('addSubject',compact('student','board_roll','reg_no'));
                    }
                }
            }
        }
    }