<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;

/**
 * Class SignatureContainer
 *
 * @package App\Models
 * @mixin Eloquent
 * @property int $id
 * @property string $container_type
 * @property string|null $container_path
 * @property string $public_id
 * @property UnsignedFile[]|Collection $files
 * @property ContainerSigner[]|Collection $signers
 * @property string $company_id
 * @property Company $company
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class SignatureContainer extends Model
{
    use HasFactory;

    public const LEVEL_OWNER = "owner";
    public const LEVEL_SIGNER = "signer";
    public const LEVEL_VIEWER = "viewer";

    public const ACCESS_WHITELIST = "whitelist";
    public const ACCESS_PUBLIC = "public";

    public function files()
    {
        return $this->hasMany(UnsignedFile::class);
    }

    public function signers()
    {
        return $this->hasMany(ContainerSigner::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function addConfirmationPage()
    {
        $pdf          = new Fpdi();
        $unsignedFile = $this->files->first();

        $pageCount = $pdf->setSourceFile(StreamReader::createByString(Storage::get($unsignedFile->storagePath())));

        // Read whole PDF apply signature to correct page
        for ($i = 0; $i <= $pageCount; $i++) {
            $pdf->AddPage();
            $imported = $pdf->importPage(1);
            $pdf->useImportedPage($imported, 0, 0);
        }

        $pdf->AddPage();
        $pdf->Ln();
//        foreach ($this->signers as $signer) {
//            $pdf->SetFontSize(20);
//            $pdf->Write(26, "Signer $signer->identifier: \n");
//            $pdf->SetFontSize(14);
//            foreach ($signer->auditTrail as $trail) {
//                $pdf->Write(20, "Time: $trail->created_at, IP=$trail->id, Action: $trail->identifier, Device info= " . $trail->system_info['browser_name_pattern'] . "\n");
//            }
//            $pdf->Ln();
//        }

        // Overwrite existing PDF if not changed
        $pdfContents = $pdf->Output('S');

        Storage::put($unsignedFile->storagePath(), $pdfContents);

        info("Confirmation page created for $this->public_id");
    }
}
