Shopware.Service('privileges')
    .addPrivilegeMappingEntry({
        category: 'permissions',
        parent: 'customers',
        key: 'customer',
        roles: {
            viewer: {
                privileges: [
                    'customer:read',
                    'customer_address:read',
                    'customer_group:read',
                    'salutation:read',
                    'sales_channel:read',
                    'payment_method:read',
                    'country:read',
                    'country_state:read',
                    'tag:read',
                    'order:read',
                    'order_customer:read',
                    'custom_field_set:read',
                    'custom_field:read',
                    'custom_field_set_relation:read',
                    'state_machine_state:read',
                    'currency:read'
                ],
                dependencies: []
            },
            editor: {
                privileges: [
                    'customer:update',
                    'customer_address:update',
                    'customer_address:create',
                    'customer_address:delete',
                    'tag:create',
                    'customer_tag:create',
                    'customer_group:update',
                    'custom_field:update'
                ],
                dependencies: [
                    'customer.viewer'
                ]
            },
            creator: {
                privileges: [
                    'customer:create'
                ],
                dependencies: [
                    'customer.viewer',
                    'customer.editor'
                ]
            },
            deleter: {
                privileges: [
                    'customer:delete'
                ],
                dependencies: [
                    'customer.viewer'
                ]
            }
        }
    });
