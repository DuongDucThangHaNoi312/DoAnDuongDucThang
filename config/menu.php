<?php

return [
    [
        'name' => 'organizations.label',
        'glyphicon' => 'fas fa-city fa-fw',
        'hide' => false,
        'permissions'   => ['companies.read', 'departments.read', 'combined.read', 'titles.read', 'positions.read'],
        'child' => [
            [
                'name' => 'organizations.companies',
                'route' => 'admin.companies.index',
                'glyphicon' => 'fas fa-building fa-fw',
                'hide' => false,
                'permissions'   => ['companies.read'],
            ],
            [
                'name' => 'organizations.departments',
                'permissions'   => ['departments.read'] ,
                'route' => 'admin.departments.index',
                'glyphicon' => 'fas fa-house-damage fa-fw',
                'hide' => false,

            ],
            [
                'name' => 'organizations.departmentGroups',
                'permissions'   => ['combined.read'] ,
                'route' => 'admin.combined.index',
                'glyphicon' => 'fas fa-layer-group fa-fw',
                'hide' => false,
            ],
            [
                'name' => 'organizations.title',
                'permissions'   => ['titles.read'] ,
                'route' => 'admin.titles.index',
                'glyphicon' => 'fas fa-user-tag fa-fw',
                'hide' => false,
            ],
            [
                'name' => 'organizations.position',
                'permissions'   => ['positions.read'] ,
                'route' => 'admin.positions.index',
                'glyphicon' => 'fas fa-user-tie fa-fw',
                'hide' => false
            ],
        ]
    ],
    [
        'name' => 'human-management.label',
        'glyphicon' => 'fas fa-user-shield fa-fw',
        'hide' => false,
        'permissions'   => ['staffs.read', 'contracts.read'],
        'child' => [
			[
				'name' => 'human-management.staffs',
				'route' => 'admin.staffs.index',
				'glyphicon' => 'fas fa-users fa-fw',
				'hide' => false,
                'permissions'   => ['staffs.read'] ,
            ],
        	[
				'name' => 'human-management.contract',
				'route' => 'admin.contracts.index',
                'permissions'   => ['contracts.read'] ,
                'glyphicon' => 'fas fa-file-contract fa-fw',
				'hide' => false,
			],
        ]
    ],
    [
        'name' => 'timekeeping.label',
        'glyphicon' => 'fas fa-calculator',
        'hide' => false,
        'child' => [
            [
				'name' => 'newborn.label',
				'route' => 'admin.newborns.index',
                'permissions'   => ['newborns.read'] ,
                'glyphicon' => 'fas fa-baby-carriage',
				'hide' => false,
			],
            [
                'name' => 'timekeeping.setup',
                'route' => 'admin.setupshifts.index',
                'glyphicon' => 'fas fa-briefcase fa-fw',
                'hide' => false,
                'permissions'   => ['timekeeping.create'] ,
            ],
            [
                'name' => 'timekeeping.schedule',
                'route' => 'admin.workschedule.index',
                'glyphicon' => 'fas fa-clock fa-fw',
                'hide' => false,
                'permissions'   => ['workschedule.read'] ,
            ],
            [
                'name' => 'timekeeping.list',
                'route' => 'admin.timekeeping.index',
                'glyphicon' => 'fas fa-clipboard-list fa-fw',
                'hide' => false,
                'permissions'   => ['timekeeping.read'] ,
            ],
            [
                'name' => 'timekeeping.ot',
                'route' => 'admin.ot.index',
                'glyphicon' => 'fas fa-clock fa-fw',
                'hide' => false,
                'permissions'   => ['ot.read'] ,
            ],
        ]
    ],
    [
        'name' => 'salary.label',
        'glyphicon' => 'fas fa-money-bill-wave fa-fw',
        'hide' => false,
        'permissions'   => ['allowance_categories.read', 'payrolls.read','deductions.read'],
        'child' => [
            [
                'name' => 'allowance_categories.label',
                'route' => 'admin.allowance-categories.index',
                'glyphicon' => 'fas fa-coins fa-fw',
                'hide' => false,
                'permissions'   => ['allowance_categories.read'] ,
            ],

            [
                'name' => 'adjustments.label',
                'glyphicon' => 'fab fa-font-awesome',
                'route' => 'admin.adjustments.index',
                'hide' => false,
                'role' => ['system'],
            ],
          
            [
                'name' => 'salary.deduction',
                'route' => 'admin.deductions.index',
                'glyphicon' => 'fas fa-dollar-sign fa-fw',
                'hide' => false,
                'permissions'   => ['deductions.read'] ,
            ],

            [
                'name' => 'salary.union_funds',
                'route' => 'admin.unionfunds.index',
                'glyphicon' => 'fas fa-dollar-sign fa-fw',
                'hide' => false,
                'permissions'   => ['unionfunds.read'] ,
            ],

            [
                'name' => 'payoffs.label',
                'route' => 'admin.payoffs.index',
                'glyphicon' => 'fas fa-dollar-sign fa-fw',
                'hide' => false,
                'permissions'   => ['payoffs.create', 'payoffs.read'] ,
            ],
            [
                'name' => 'impales.label',
                'route' => 'admin.impales.create',
                'glyphicon' => 'fas fa-dollar-sign fa-fw',
                'hide' => false,
                'permissions'   => ['impales.create'] ,
            ],
            [
                'name' => 'payroll.label',
                'route' => 'admin.payrolls.index',
                'glyphicon' => 'fas fa-dollar-sign fa-fw',
                'hide' => false,
                'permissions'   => ['payrolls.read'] ,
            ],
            [
                'name' => 'payroll.vans',
                'route' => 'admin.vans.index',
                'glyphicon' => 'fas fa-shuttle-van',
                'hide' => false,
                'permissions'   => ['vans.read'] ,
            ],
            [
                'name' => 'payroll.driver',
                'route' => 'admin.drivers.index',
                'glyphicon' => 'fas fa-file-invoice-dollar',
                'hide' => false,
                'permissions'   => ['drivers.read'] ,
            ],
            [
                'name' => 'payroll.insurance_allocation',
                'route' => 'admin.insurances.index',
                'glyphicon' => 'fas fa-file-invoice-dollar',
                'hide' => false,
                'permissions'   => ['insurances.read'] ,
            ],
        ]
    ],

    // [
    //     'name' => 'payroll.user',
    //     'route' => 'admin.payrolls.salary_user',
    //     'glyphicon' => 'fas fa-money-bill-wave fa-fw',
    //     'hide' => false,
    //     // 'permissions'   => ['payrolls.salary_user'],
    // ],

    [
        'name' => 'payroll.user',
        'route' => 'admin.payrolls1.salary_user',
        'glyphicon' => 'fas fa-money-bill-wave fa-fw',
        'hide' => false,
    ],
   
    // [
    //     'name' => 'salary_choose_container',
    //     'route' => 'admin.salary-choose-containers.index',
    //     'glyphicon' => 'fas fa-money-bill-wave fa-fw',
    //     'hide' => false,
    // ],

    // [
    //     'name' => 'salary_declaration',
    //     'route' => 'admin.salary-declarations.index',
    //     'glyphicon' => 'fas fa-money-bill-wave fa-fw',
    //     'hide' => false,
    // ],

    [
        'name' => 'calendar.label',
        'glyphicon' => 'fas fa-calendar-alt fa-fw',
        'permissions'   => ['schedules.read', 'overtimes.read','take_leave.staffs.read','workschedule.read','manager.leave.read'],
        'hide' => false,
        'child' => [
            [
                'name' => 'human-management.schedule-work',
                'route' => 'admin.schedules.index',
                'permissions'   => ['schedules.read'] ,
                'glyphicon' => 'fas fa-calendar-minus fa-fw',
                'hide' => false,
            ],
            [
                'name' => 'human-management.overtime',
                'route' => 'admin.overtimes.index',
                'glyphicon' => 'fas fa-calendar-plus fa-fw',
                'hide' => false,
                'permissions'   => ['overtimes.read'] ,
            ],
            [
                'name' => 'staffs.take-leave',
                'route' => 'admin.take-leave.staffs.index',
                'glyphicon' => 'fa fa-list fa-fw',
                'hide' => false,
                'permissions'   => ['take_leave.staffs.read'] ,
            ],

            
            [
                'name' => 'human-management.approval-leave',
                'route' => 'admin.manager.leave.index',
                'glyphicon' => 'fas fa-calendar-check fa-fw',
                'hide' => false,
                'permissions'   => ['manager.leave.read'] ,
            ],
        ]
    ],
    [
        'name' => 'set_kpi',
        'glyphicon' => 'fas fa-chart-line fa-fw',
        'route' => 'admin.targets.index',
        'hide' => false,
        'permissions'   => ['targets.read'] ,
    ],
    // [
    //     'name' => 'managements.label',
    //     'glyphicon' => 'fas fa-user-tag',
    //     'route' => 'admin.managements.index',
    //     'hide' => false,
    //     'role' => ['system'],
    // ],
    [
        'name' => 'reports.label',
        'glyphicon' => 'fas fa-file-excel fa-fw',
        'route' => 'admin.reports.index',
        'hide' => false,
        'permissions'   => ['reports.read'],
//        'role' => ['system'],
    ],
    [
        'name' => 'roles.label',
        'glyphicon' => 'fas fa-cogs fa-fw',
        'route' => 'admin.roles.index',
        'hide' => false,
        'role' => ['system'],
    ],
    
];
