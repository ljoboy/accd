<?php

namespace App\Jobs;

use App\Enums\Status;
use App\Models\User;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class GenerateCardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     */
    public function __construct(private readonly User $user)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->user;

        // Charger le fond d'image
        $backgroundPath = public_path('images/card_back.png');
        $img = Image::read($backgroundPath);

        // Ajouter l'avatar
        $avatarPath = Storage::disk('public')->path($user->avatar);
        $avatar = Image::read($avatarPath);
        $img->place($avatar, 'top-left', 50, 50);

        // Ajouter le nom, prénom, et postnom
        $img->text($user->name . ' ' . $user->prenom . ' ' . $user?->postnom, 600, 80, function ($font) {
            $font->file(public_path('fonts/Arial.ttf'));
            $font->size(24);
            $font->color('#000000');
            $font->align('left');
            $font->valign('top');
        });

        // Générer le code QR
        $qrCode = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($user->qr_code)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->size(400)
            ->margin(20)
            ->build();

        // TODO : Mettre dans un dossier temporaire
        $qrCodePath = Storage::disk('public')->path('qr_codes/');
        if (!file_exists($qrCodePath)) {
            mkdir($qrCodePath);
        }
        $qrCodePath .= uniqid() . '.png';
        $qrCode->saveToFile($qrCodePath);

        $qrCodeImage = Image::read($qrCodePath);
        $img->place($qrCodeImage, 'top-left', 800, 150);

        // Enregistrer l'image générée
        $generatedImagePath = public_path('images/cards/' . uniqid($user->name . '_') . '.png');
        $img->save($generatedImagePath);
        $user->card->url = $generatedImagePath;
        $user->card->status = Status::Accepted;
        $user->card->save();

        unlink($qrCodePath);
    }
}
