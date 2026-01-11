<?php

namespace ForteTestHarness\Tests\Integration;

use ForteTestHarness\Tests\Support\IntegrationTestCase;

/**
 * @group integration
 */
class RestSandboxCustomerVariantFlowTest extends IntegrationTestCase
{
    public function testCustomerVariantsFlow(): void
    {
        $billing = $this->defaultBillingAddress();

        $variants = [
            [
                'label' => 'customer simple',
                'payload' => [
                    'first_name' => $billing['first_name'],
                    'last_name' => $billing['last_name'],
                    'company_name' => 'Forte Harness',
                    'customer_id' => $this->buildOrderNumber('customer-simple'),
                ],
            ],
            [
                'label' => 'customer first/last only',
                'payload' => [
                    'first_name' => $billing['first_name'],
                    'last_name' => $billing['last_name'],
                ],
            ],
            [
                'label' => 'customer with addresses',
                'payload' => [
                    'first_name' => $billing['first_name'],
                    'last_name' => $billing['last_name'],
                    'company_name' => 'Forte Harness',
                    'addresses' => [
                        [
                            'label' => 'Billing',
                            'first_name' => $billing['first_name'],
                            'last_name' => $billing['last_name'],
                            'phone' => $billing['phone'],
                            'email' => $billing['email'],
                            'address_type' => 'default_billing',
                            'physical_address' => $billing['physical_address'],
                        ],
                        [
                            'label' => 'Shipping',
                            'first_name' => $billing['first_name'],
                            'last_name' => $billing['last_name'],
                            'phone' => $billing['phone'],
                            'email' => $billing['email'],
                            'address_type' => 'default_shipping',
                            'physical_address' => $billing['physical_address'],
                        ],
                    ],
                ],
            ],
            [
                'label' => 'customer with paymethod',
                'payload' => [
                    'first_name' => $billing['first_name'],
                    'last_name' => $billing['last_name'],
                    'company_name' => 'Forte Harness',
                    'paymethod' => [
                        'label' => 'Visa - ' . $this->buildOrderNumber('cust-paymethod'),
                        'notes' => 'Integration paymethod',
                        'card' => $this->buildCardPayload(),
                    ],
                ],
            ],
            [
                'label' => 'customer with addresses and paymethod',
                'payload' => [
                    'first_name' => $billing['first_name'],
                    'last_name' => $billing['last_name'],
                    'company_name' => 'Forte Harness',
                    'addresses' => [
                        [
                            'label' => 'Billing',
                            'first_name' => $billing['first_name'],
                            'last_name' => $billing['last_name'],
                            'phone' => $billing['phone'],
                            'email' => $billing['email'],
                            'address_type' => 'default_billing',
                            'physical_address' => $billing['physical_address'],
                        ],
                        [
                            'label' => 'Shipping',
                            'first_name' => $billing['first_name'],
                            'last_name' => $billing['last_name'],
                            'phone' => $billing['phone'],
                            'email' => $billing['email'],
                            'address_type' => 'default_shipping',
                            'physical_address' => $billing['physical_address'],
                        ],
                    ],
                    'paymethod' => [
                        'label' => 'Visa - ' . $this->buildOrderNumber('cust-paymethod-addr'),
                        'notes' => 'Integration paymethod',
                        'card' => $this->buildCardPayload(),
                    ],
                ],
            ],
        ];

        $paypalToken = $this->optionalEnvValue('FORTE_TEST_PAYPAL_BILLING_TOKEN', '');
        if ($paypalToken !== '') {
            $variants[] = [
                'label' => 'customer with paypal paymethod',
                'payload' => [
                    'first_name' => $billing['first_name'],
                    'last_name' => $billing['last_name'],
                    'company_name' => 'Forte Harness',
                    'paymethod' => [
                        'label' => 'PayPal - ' . $this->buildOrderNumber('cust-paypal'),
                        'notes' => 'Integration PayPal',
                        'vendor' => [
                            'vendor_type' => 'paypal',
                            'vendor_billing_agreement_token' => $paypalToken,
                        ],
                    ],
                ],
            ];
            $variants[] = [
                'label' => 'customer with addresses and paypal',
                'payload' => [
                    'first_name' => $billing['first_name'],
                    'last_name' => $billing['last_name'],
                    'company_name' => 'Forte Harness',
                    'addresses' => [
                        [
                            'label' => 'Billing',
                            'first_name' => $billing['first_name'],
                            'last_name' => $billing['last_name'],
                            'phone' => $billing['phone'],
                            'email' => $billing['email'],
                            'address_type' => 'default_billing',
                            'physical_address' => $billing['physical_address'],
                        ],
                        [
                            'label' => 'Shipping',
                            'first_name' => $billing['first_name'],
                            'last_name' => $billing['last_name'],
                            'phone' => $billing['phone'],
                            'email' => $billing['email'],
                            'address_type' => 'default_shipping',
                            'physical_address' => $billing['physical_address'],
                        ],
                    ],
                    'paymethod' => [
                        'label' => 'PayPal - ' . $this->buildOrderNumber('cust-paypal-addr'),
                        'notes' => 'Integration PayPal',
                        'vendor' => [
                            'vendor_type' => 'paypal',
                            'vendor_billing_agreement_token' => $paypalToken,
                        ],
                    ],
                ],
            ];
        }

        $created = [];
        foreach ($variants as $variant) {
            $response = $this->client->request(
                'POST',
                '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers',
                $variant['payload']
            );

            $this->assertResponse2xx($response, 'create ' . $variant['label']);
            $customerToken = $response['data']['customer_token'] ?? '';
            $this->assertNotSame('', $customerToken, 'Missing customer_token from ' . $variant['label']);
            $created[] = $customerToken;
        }

        foreach ($created as $customerToken) {
            $delete = $this->client->request(
                'DELETE',
                '/organizations/' . $this->organizationId . '/locations/' . $this->locationId . '/customers/' . $customerToken
            );
            $this->assertResponse2xx($delete, 'delete customer (variant)');
        }

        if ($this->optionalEnvFlag('FORTE_TEST_ENABLE_LOCATIONLESS_CUSTOMER')) {
            $locationlessPayload = [
                'first_name' => $billing['first_name'],
                'last_name' => $billing['last_name'],
                'company_name' => 'Forte Harness',
                'organization_id' => $this->organizationId,
            ];

            $response = $this->client->request(
                'POST',
                '/customers',
                $locationlessPayload
            );

            $this->assertResponse2xx($response, 'create locationless customer');
            $customerToken = $response['data']['customer_token'] ?? '';
            if ($customerToken !== '') {
                $delete = $this->client->request(
                    'DELETE',
                    '/customers/' . $customerToken
                );
                $this->assertResponse2xx($delete, 'delete locationless customer');
            }
        }
    }
}
