<?php
namespace frontend\models;

use yii\base\Model;
use common\models\Old\Contractor;


class ContractorSearch extends Model
{
    public $name;
    public $inn;
    public $phone;
    public $email;

    public $page;

    public function rules()
    {
        return [
            [['name', 'inn', 'phone', 'email', 'page'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels() {
        return [
            'name' => 'Название',
            'inn' => 'ИНН',
            'email' => 'Email',
            'phone' => 'Телефон',
        ];
    }

    public function search($params)
    {
        $query = Contractor::find();
        $query->joinWith('organizations');
        $query->joinWith('contact_persons');

        $this->load($params);
        if (!$this->validate() || (empty($this->name) && empty($this->phone) && empty($this->inn) && empty($this->email))) {
            return false;
        }

        if (!empty($this->name)) {
            $pieces = explode(' ', $this->name);

            $expression = ['or'];

            $condition = ['or'];
            foreach ($pieces as $piece) {
                $condition[] = ['like', 'contractor_name', $piece];
            }
            $expression[] = $condition;

            $condition = ['or'];
            foreach ($pieces as $piece) {
                $condition[] = ['like', 'organization.organization_name', $piece];
            }
            $expression[] = $condition;

            $condition = ['or'];
            foreach ($pieces as $piece) {
                $condition[] = ['like', 'contact_person.contact_person_name', $piece];
            }
            $expression[] = $condition;

            $query->andWhere(['or', $expression]);
        }

        if (!empty($this->inn)) {
            $query->andWhere(['like', 'organization.inn', $this->inn]);
        }

        if (!empty($this->email)) {
            $query->andWhere(['or',
                ['like', 'organization.email', $this->email],
                ['like', 'contact_person.email', $this->email],
            ]);
        }

        if (!empty($this->phone)) {
            $phone = preg_replace('/-(?!\d)|_|(?<!\d)\)(?!\d)/', '', $this->phone);
            //preg_match("/\([0-9]{0,3}\)?[0-9]{0,3}\-?[0-9]{0,4}/", $this->phone, $matches);
            if (true) {
                $expression = ['or'];

                $condition = ['and'];
                $condition[] = ['like', 'organization.phone_number', $phone];
                $expression[] = $condition;

                $condition = ['and'];
                $condition[] = ['like', 'contact_person.phone_number', $phone];
                $expression[] = $condition;

                $condition = ['and'];
                $condition[] = ['like', 'contact_person.mobile_phone_number', $phone];

                $expression[] = $condition;

                $query->andWhere(['or', $expression]);
            } else {
                $this->phone = null;
            }
        }

        return $query;
    }
    
    public function indentKeywords($contractors) {
        $phone = preg_replace('/-(?!\d)|_|(?<!\d)\)(?!\d)/', '', $this->phone);

        foreach ($contractors as $contractor) {
            if (!empty($this->name)) {
                $pieces = explode(' ', $this->name);
                foreach($pieces as $piece) {
                    $contractor->contractor_name = preg_replace(
                        "/(" . $piece . ")/ui",
                        '<span class="keyword">${1}</span>',
                        $contractor->contractor_name
                    );
                }
            }

            foreach ($contractor->organizations as $organization) {
                if (!empty($this->name)) {
                    $pieces = explode(' ', $this->name);
                    foreach($pieces as $piece) {
                        $organization->organization_name = preg_replace(
                            "/(" . $piece . ")/ui",
                            '<span class="keyword">${1}</span>',
                            $organization->organization_name
                        );
                    }
                }

                if (!empty($this->inn)) {
                    $organization->inn = preg_replace(
                        "/(" . $this->inn . ")/ui",
                        '<span class="keyword">${1}</span>',
                        $organization->inn
                    );
                }

                if (!empty($this->email)) {
                    $organization->email = preg_replace(
                        "/(" . $this->email . ")/ui",
                        '<span class="keyword">${1}</span>',
                        $organization->email
                    );
                }

                if (!empty($this->phone)) {
                    $organization->phone_number = str_replace(
                        $phone,
                        '<span class="keyword">' . $phone . '</span>',
                        $organization->phone_number
                    );
                }
            }

            foreach ($contractor->contact_persons as $contact_person) {
                if (!empty($this->name)) {
                    $pieces = explode(' ', $this->name);
                    foreach ($pieces as $piece) {
                        $contact_person->contact_person_name = preg_replace(
                            "/(" . $piece . ")/ui",
                            '<span class="keyword">${1}</span>',
                            $contact_person->contact_person_name
                        );
                    }
                }

                if (!empty($this->email)) {
                    $contact_person->email = preg_replace(
                        "/(" . $this->email . ")/ui",
                        '<span class="keyword">${1}</span>',
                        $contact_person->email
                    );
                }

                if (!empty($this->phone)) {
                    $contact_person->phone_number = str_replace(
                        $phone,
                        '<span class="keyword">' . $phone . '</span>',
                        $contact_person->phone_number
                    );

                    $contact_person->mobile_phone_number = str_replace(
                        $phone,
                        '<span class="keyword">' . $phone . '</span>',
                        $contact_person->mobile_phone_number
                    );
                }
            }
        }
    }
}
