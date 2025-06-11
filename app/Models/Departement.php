<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;

    class Departement extends Model
    {
        use HasFactory;

        protected $fillable = ['nom', 'description'];

        // Existing relationships
        public function users()
        {
            return $this->hasMany(User::class);
        }

        public function filieres()
        {
            return $this->hasMany(Filiere::class);
        }

        public function unitesEnseignement()
        {
            return $this->hasMany(UniteEnseignement::class);
        }

        // Get the department head
        public function chef()
        {
            return $this->users()->where('role', 'chef')->first();
        }

        // Set a new department head
        public function setChef($userId)
        {
            DB::transaction(function () use ($userId) {
                // Reset current chef's role to enseignant
                $this->users()->where('role', 'chef')->update(['role' => 'enseignant']);

                // Set new chef
                User::where('id', $userId)->update(['role' => 'chef']);
            });

            return true;
        }
    }
