package com.bubbinator91.opentec.activities;

import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.BitmapDrawable;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Base64;
import android.util.Log;
import android.view.MotionEvent;
import android.view.View;
import android.widget.ImageView;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.bubbinator91.opentec.R;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.MapFragment;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;
import com.jeremyfeinstein.slidingmenu.lib.SlidingMenu;
import com.jeremyfeinstein.slidingmenu.lib.app.SlidingActivity;

import org.apache.http.client.HttpClient;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.ByteArrayOutputStream;
import java.io.InputStream;
import java.util.ArrayList;

public class MainActivity extends SlidingActivity {
    private GoogleMap mMap;
    private ArrayList<MarkerData> markerData;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        setBehindContentView(R.layout.event_detail);
        markerData = new ArrayList<>();

        try {
            initializeMap();
            if (mMap != null) {
                mMap.setMyLocationEnabled(true);
                mMap.setMapType(GoogleMap.MAP_TYPE_HYBRID);
                mMap.setPadding(0,0,0,130);
            }
            SlidingMenu sm = getSlidingMenu();
            sm.setMode(SlidingMenu.RIGHT);
            sm.setTouchModeAbove(SlidingMenu.TOUCHMODE_NONE);
            sm.setBehindOffsetRes(R.dimen.slidingmenu_offset);
            sm.setShadowWidthRes(R.dimen.shadow_width);
            sm.setShadowDrawable(R.drawable.shadow);
            sm.setFadeDegree(0.35f);

            mMap.setOnMarkerClickListener(new GoogleMap.OnMarkerClickListener() {
                @Override
                public boolean onMarkerClick(Marker marker) {
                    for(MarkerData md : markerData) {
                        if (marker.getId().equals(md.id)) {
                            ProgressBar spinner = (ProgressBar)findViewById(R.id.event_wiggle_data_progressBar);
                            spinner.setVisibility(View.VISIBLE);

                            TextView tv = (TextView)findViewById(R.id.event_location);
                            tv.setText(md.location);
                            tv = (TextView)findViewById(R.id.event_magnitude);
                            tv.setText(md.magnitude);
                            tv = (TextView)findViewById(R.id.event_magnitudetype);
                            tv.setText(md.magnitudeType);
                            tv = (TextView)findViewById(R.id.event_depth);
                            tv.setText(md.depth + " KM");
                            tv = (TextView)findViewById(R.id.event_latitude);
                            tv.setText(md.lat);
                            tv = (TextView)findViewById(R.id.event_longitude);
                            tv.setText(md.lng);
                            tv = (TextView)findViewById(R.id.event_timestamp);
                            tv.setText(md.timestamp);
                            tv = (TextView)findViewById(R.id.event_cause);
                            tv.setText(md.cause);

                            final ImageView iv = (ImageView)findViewById(R.id.event_wiggle_data);
                            iv.setImageBitmap(null);
                            iv.setOnTouchListener(new View.OnTouchListener() {
                                @Override
                                public boolean onTouch(View v, MotionEvent event) {
                                    Log.d("ImageView.OnTouchListener", "image touched");
                                    Intent intent = new Intent(getApplicationContext(), ImageActivity.class);
                                    ByteArrayOutputStream byteArrayOutputStream = new ByteArrayOutputStream();
                                    Bitmap bmp = ((BitmapDrawable)iv.getDrawable()).getBitmap();
                                    bmp.compress(Bitmap.CompressFormat.PNG, 50, byteArrayOutputStream);
                                    intent.putExtra("image", byteArrayOutputStream.toByteArray());
                                    startActivity(intent);
                                    return false;
                                }
                            });
                            String url = "http://service.iris.edu/irisws/timeseries/1/query?net="
                                    + md.network + "&sta=" + md.station + "&loc=00&cha=BHZ&start="
                                    + md.start + "&end=" + md.end + "&output=plot&width=800&height=800";
                            Log.d("OnMarkerClick", url);
                            new DownloadImageTask(iv, spinner).execute(url);

                            getSlidingMenu().showMenu();
                            return true;
                        }
                    }
                    return false;
                }
            });
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    protected void onResume() {
        super.onResume();
        initializeMap();
    }

    private void initializeMap() {
        if (mMap == null) {
            mMap = ((MapFragment)getFragmentManager().findFragmentById(R.id.map)).getMap();
            if (mMap == null) {
                Toast.makeText(getApplicationContext(), "Unable to create map", Toast.LENGTH_SHORT).show();
            } else {
                if (markerData != null) {
                    displayMarkerData();
                }
            }
        }
    }

    private void displayMarkerData() {
        if (mMap != null) {
            mMap.clear();
            for (int i = 0; i < markerData.size(); ++i) {
                MarkerData md = markerData.get(i);
                MarkerOptions marker = new MarkerOptions()
                        .position(new LatLng(Double.parseDouble(md.lat), Double.parseDouble(md.lng)))
                        .title(md.location);
                double depth = Double.parseDouble(md.depth);
                depth = (depth / 1000.0);
                if (depth <= 100.0)
                    marker.icon(BitmapDescriptorFactory.fromResource(R.drawable.yellow_circle));
                else if (depth <= 200.0)
                    marker.icon(BitmapDescriptorFactory.fromResource(R.drawable.green_circle));
                else if (depth <= 400.0)
                    marker.icon(BitmapDescriptorFactory.fromResource(R.drawable.blue_circle));
                else if (depth <= 600.0)
                    marker.icon(BitmapDescriptorFactory.fromResource(R.drawable.purple_circle));
                else
                    marker.icon(BitmapDescriptorFactory.fromResource(R.drawable.red_circle));

                Marker m = mMap.addMarker(marker);
                md.id = m.getId();
                markerData.set(i, md);
            }
        }
    }

    public void getEvents(View view) {
        if (mMap != null) {
            mMap.clear();
            GetEventsTask task = new GetEventsTask();
            task.execute(new String(" "));

            displayEvents();
        }
    }

    private void displayEvents() {

    }

    private class GetEventsTask extends AsyncTask<String, Void, String> {
        @Override
        protected String doInBackground(String... urls) {
            HttpClient httpClient = new DefaultHttpClient();
            HttpGet httpGet = new HttpGet("http://opentec.cs.purdue.edu/api/android_get_events_24hrs.php");
            String credentials = "username:password";
            String base64EncodedCredentials = Base64.encodeToString(credentials.getBytes(), Base64.NO_WRAP);
            httpGet.addHeader("Authorization", "Basic " + base64EncodedCredentials);
            try {
                ResponseHandler<String> responseHandler = new BasicResponseHandler();
                return httpClient.execute(httpGet, responseHandler);
            } catch (Exception e) {
                e.printStackTrace();
            }

            return null;
        }

        @Override
        protected void onPostExecute(String result) {
            if (result == null) {
                Log.d("MainActivity.GetEventsTask.onPostExecute", "404 error");
                Toast.makeText(getApplicationContext(),"Could not connect to server", Toast.LENGTH_SHORT).show();
            } else {
                if (result.equals("failure_db_connect")) {
                    Log.d("MainActivity.GetEventsTask.onPostExecute", "serverside database error");
                    Toast.makeText(getApplicationContext(),"Internal server error", Toast.LENGTH_SHORT).show();
                } else if (result.equals("failure_no_new_events")) {
                    Toast.makeText(getApplicationContext(),"THere are no new events to display", Toast.LENGTH_SHORT).show();
                } else {
                    try {
                        JSONObject jsonEvents = new JSONObject(result);
                        JSONArray jsonEventsArray = jsonEvents.getJSONArray("events");
                        markerData = new ArrayList<>();

                        for (int i = 0; i < jsonEventsArray.length(); i++) {
                            try {
                                JSONObject event = jsonEventsArray.getJSONObject(i);
                                /*String s = "\nEvent " + i + ":\n\tMagnitude: " + event.getString("magnitude")
                                        + "\n\tMagnitude type: " + event.getString("magnitudetype")
                                        + "\n\tDepth: " + event.getString("depth")
                                        + "\n\tLatitude: " + event.getString("latitude")
                                        + "\n\tLongitude: " + event.getString("longitude")
                                        + "\n\tLocation: " + event.getString("location")
                                        + "\n\tTimestamp: " + event.getString("timestamp")
                                        + "\n\tCause: " + event.getString("cause");
                                Log.d("MainActivity.GetEventsTask.onPostExecute", s);*/

                                MarkerOptions marker = new MarkerOptions()
                                        .position(new LatLng(event.getDouble("latitude"), event.getDouble("longitude")))
                                        .title(event.getString("location"));
                                double depth = event.getDouble("depth");
                                depth = (depth / 1000.0);
                                if (depth <= 100.0)
                                    marker.icon(BitmapDescriptorFactory.fromResource(R.drawable.yellow_circle));
                                else if (depth <= 200.0)
                                    marker.icon(BitmapDescriptorFactory.fromResource(R.drawable.green_circle));
                                else if (depth <= 400.0)
                                    marker.icon(BitmapDescriptorFactory.fromResource(R.drawable.blue_circle));
                                else if (depth <= 600.0)
                                    marker.icon(BitmapDescriptorFactory.fromResource(R.drawable.purple_circle));
                                else
                                    marker.icon(BitmapDescriptorFactory.fromResource(R.drawable.red_circle));

                                Marker m = mMap.addMarker(marker);
                                MarkerData md = new MarkerData();
                                md.id = m.getId();
                                md.magnitude = event.getString("magnitude");
                                md.magnitudeType = event.getString("magnitudetype");
                                md.depth = String.valueOf(depth);
                                md.lat = event.getString("latitude");
                                md.lng = event.getString("longitude");
                                md.location = event.getString("location");
                                md.timestamp = event.getString("timestamp");
                                md.cause = event.getString("cause");
                                md.network = event.getString("network");
                                md.station = event.getString("station");
                                md.start = event.getString("before");
                                md.end = event.getString("after");
                                markerData.add(md);
                            } catch (JSONException e) {
                                e.printStackTrace();
                            }
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }
            }
        }
    }

    private class MarkerData {
        String id;
        String magnitude;
        String magnitudeType;
        String lat;
        String lng;
        String depth;
        String location;
        String timestamp;
        String cause;
        String network;
        String station;
        String start;
        String end;
    }

    private class DownloadImageTask extends AsyncTask<String, Void, Bitmap> {
        private ImageView mImageView;
        private ProgressBar mSpinner;

        public DownloadImageTask(ImageView imageView, ProgressBar spinner) {
            mImageView = imageView;
            mSpinner = spinner;
        }

        protected Bitmap doInBackground(String... urls) {
            Bitmap mIcon = null;
            try {
                InputStream in = new java.net.URL(urls[0]).openStream();
                mIcon = BitmapFactory.decodeStream(in);
            } catch (Exception e) {
                Log.e("", e.getMessage());
                e.printStackTrace();
            }
            return mIcon;
        }

        protected void onPostExecute(Bitmap result) {
            mSpinner.setVisibility(View.GONE);
            mImageView.setImageBitmap(result);
        }
    }
}
