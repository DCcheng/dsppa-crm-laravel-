<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | following language lines contain default error messages used by
    | validator class. Some of these rules have multiple versions such
    | as size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute must be accepted.',
    'active_url'           => ':attribute is not a valid URL.',
    'after'                => ':attribute must be a date after :date.',
    'after_or_equal'       => ':attribute must be a date after or equal to :date.',
    'alpha'                => ':attribute may only contain letters.',
    'alpha_dash'           => ':attribute may only contain letters, numbers, and dashes.',
    'alpha_num'            => ':attribute may only contain letters and numbers.',
    'array'                => ':attribute必须是数组类型',
    'before'               => ':attribute must be a date before :date.',
    'before_or_equal'      => ':attribute must be a date before or equal to :date.',
    'between'              => [
        'numeric' => ':attribute must be between :min and :max.',
        'file'    => ':attribute must be between :min and :max kilobytes.',
        'string'  => ':attribute must be between :min and :max characters.',
        'array'   => ':attribute must have between :min and :max items.',
    ],
    'boolean'              => ':attribute必须是Boolean类型',
    'confirmed'            => ':attribute confirmation does not match.',
    'date'                 => ':attribute is not a valid date.',
    'date_format'          => ':attribute does not match format :format.',
    'different'            => ':attribute and :other must be different.',
    'digits'               => ':attribute must be :digits digits.',
    'digits_between'       => ':attribute must be between :min and :max digits.',
    'dimensions'           => ':attribute has invalid image dimensions.',
    'distinct'             => ':attribute field has a duplicate value.',
    'email'                => ':attribute must be a valid email address.',
    'exists'               => 'selected :attribute is invalid.',
    'file'                 => ':attribute must be a file.',
    'filled'               => ':attribute field is required.',
    'image'                => ':attribute must be an image.',
    'in'                   => 'selected :attribute is invalid.',
    'in_array'             => ':attribute field does not exist in :other.',
    'integer'              => ':attribute必须是整型',
    'ip'                   => ':attribute不符合IP地址格式',
    'json'                 => ':attribute不符合JSON格式',
    'max'                  => [
        'numeric' => ':attribute大小不能超过:max.',
        'file'    => ':attribute大小不能超过:maxKB',
        'string'  => ':attribute长度不能超过:max个字符',
        'array'   => ':attribute长度不能超过:max节点',
    ],
    'mimes'                => ':attribute must be a file of type: :values.',
    'mimetypes'            => ':attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => ':attribute大小不能小于:min.',
        'file'    => ':attribute大小不能小于:minKB.',
        'string'  => ':attribute长度不能小于:min个字符',
        'array'   => ':attribute长度不能小于:min节点',
    ],
    'not_in'               => 'selected :attribute is invalid.',
    'numeric'              => ':attribute为数字类型',
    'present'              => ':attribute field must be present.',
    'regex'                => ':attribute format is invalid.',
    'required'             => ':attribute为必填项',
    'required_if'          => ':attribute field is required when :other is :value.',
    'required_unless'      => ':attribute field is required unless :other is in :values.',
    'required_with'        => ':attribute field is required when :values is present.',
    'required_with_all'    => ':attribute field is required when :values is present.',
    'required_without'     => ':attribute field is required when :values is not present.',
    'required_without_all' => ':attribute field is required when none of :values are present.',
    'same'                 => ':attribute and :other must match.',
    'size'                 => [
        'numeric' => ':attribute must be :size.',
        'file'    => ':attribute must be :size kilobytes.',
        'string'  => ':attribute must be :size characters.',
        'array'   => ':attribute must contain :size items.',
    ],
    'string'               => ':attribute must be a string.',
    'timezone'             => ':attribute must be a valid zone.',
    'unique'               => ':attribute已经被使用',
    'uploaded'             => ':attribute failed to upload.',
    'url'                  => ':attribute format is invalid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
