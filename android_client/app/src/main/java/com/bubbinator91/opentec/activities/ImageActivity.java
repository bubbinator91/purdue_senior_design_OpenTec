package com.bubbinator91.opentec.activities;

import android.app.Activity;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.Bundle;
import android.widget.ImageView;

import com.bubbinator91.opentec.R;

import uk.co.senab.photoview.PhotoViewAttacher;

public class ImageActivity extends Activity {
    private ImageView eventImage;
    private PhotoViewAttacher mAttacher;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_image);

        eventImage = (ImageView)findViewById(R.id.viewer_image);

        if (getIntent().hasExtra("image")) {
            Bitmap bmp = BitmapFactory.decodeByteArray(getIntent().getByteArrayExtra("image"), 0, getIntent().getByteArrayExtra("image").length);
            eventImage.setImageBitmap(bmp);
            mAttacher = new PhotoViewAttacher(eventImage);
        }
    }
}
