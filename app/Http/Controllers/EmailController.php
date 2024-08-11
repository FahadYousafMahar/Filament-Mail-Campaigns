<?php

namespace App\Http\Controllers;

use App\Mail\SubscriptionMail;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public $variables = [
        'username' => 'John Doe',
        'password' => '123456',
        'subscription_type' => 'New',
        'duration' => '1 month',
        'client_full_name' => 'John Doe',
        'client_email' => 'client@example.com',
        'heading_new' => 'New Subscription',
        'paragraph_new' => 'This is a new subscription. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec lacinia, nisl eget ultricies ultricies, nisl nisl aliquet nisl, eget aliquet nisl.',
        'heading_renew' => 'Renew Subscription',
        'paragraph_renew' => 'This is renewal paragraph. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec lacinia, nisl eget ultricies ultricies, nisl nisl aliquet nisl, eget aliquet nisl.',
        'user_pass' => [
            [
                'username' => 'John Doe',
                'password' => '123456'
            ],
            [
                'username' => 'jane Dove',
                'password' => '123456'
            ],
            [
                'username' => 'joe Doe',
                'password' => '123456'
            ],
        ]
    ];

    public function preview(Request $request,EmailTemplate $emailTemplate)
    {
        $data = $request->data;
        $template = $emailTemplate->template;
        $data = array_merge($this->variables,$data);
        $template_with_variables = self::makeContent($data,$template);
        return view('email-template.preview', [
            'template' => $template_with_variables
        ]);

    }

    public static function send($data): string|true
    {
        try {
            $templateObj = EmailTemplate::where('server_id', $data['server'])->first();
            if ($templateObj == null){
                throw new \Exception('Email Template not found for the Server.',1);
            }
            $data = array_merge($data,$templateObj->toArray());
            $template_with_variables = self::makeContent($data,$templateObj->template);
            Mail::to($data['client_email'])->send(new SubscriptionMail($data,$template_with_variables));
            return true;
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private static function makeContent($data,$template): string
    {
        $template_with_variables = $template;
        $replace = [];
        array_map(function($var) use ($data,&$replace) {
            if (!is_array($data[$var]))
                 $replace['{{' . $var . '}}'] = $data[$var];
        }, array_keys($data));
        $replace = array_merge($replace,[
            'src="storage' => 'src="' . url('/') . '/images',
            'url(\'storage' => 'url(\'' . url('/') . '/images',
        ]);
        if ($data['subscription_type'] == 'new') {
            $replace['{{heading}}'] = $data['heading_new'];
            $replace['{{paragraph}}'] = $data['paragraph_new'];
        } else {
            $replace['{{heading}}'] = $data['heading_renew'];
            $replace['{{paragraph}}'] = $data['paragraph_renew'];
        }

        $has_line = preg_match('/{{line}}([.\s\S]*?){{line}}/im',$template_with_variables,$matches);
        if($has_line){
            $line_template = $matches[1];
            $line_content = '';
            if(isset($data['user_pass']) && count($data['user_pass']))
            foreach ($data['user_pass'] as $key => $row) {
                if ($key >= $data['connections'])
                    break;
                if (empty($row['username']) || empty($row['password']))
                    continue;
                $userpass = ['{{username}}' => $row['username'], '{{password}}' => $row['password'], 'Your Login Details' => 'Line # '.($key+1).' Details'];
                $line_content .= str_replace(array_keys($userpass), array_values($userpass), $line_template);
            }
            $template_with_variables = preg_replace('/{{line}}([.\s\S]*?){{line}}/im',$line_content,$template_with_variables);
        }
        // find src="" and replace with storage link to images folder
        return str_replace(array_keys($replace), array_values($replace), $template_with_variables);
    }

}
