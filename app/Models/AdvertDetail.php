namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertDetail extends Model
{
    use HasFactory;
    protected $fillable =[
    'target_location',
        'maximum_target_age',
      'minimum_target_age',
      'amount_paid',
        'ad_owner',
        'postid',
        'ad_duration',
   ];
}
