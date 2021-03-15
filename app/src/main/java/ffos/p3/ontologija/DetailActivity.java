package ffos.p3.ontologija;

import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;

import androidx.appcompat.app.AppCompatActivity;

import com.google.android.gms.auth.api.signin.GoogleSignInClient;
import com.google.gson.Gson;
import com.google.gson.GsonBuilder;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.DataOutputStream;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.URL;

public class DetailActivity extends AppCompatActivity {

    private Ontologija ontologija;
    private EditText naslov;
    private EditText autor;
    private EditText duzina;
    private EditText tip;
    private Button nazad, dodaj, promjeni, obrisi;
    private RESTTask restTask;
    private Gson gson;
    GoogleSignInClient mGoogleSignInClient;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_details);
        naslov = findViewById(R.id.naslov);
        autor = findViewById(R.id.tip);
        duzina = findViewById(R.id.duzina);
        tip = findViewById(R.id.tip);
        nazad = findViewById(R.id.nazad);
        dodaj = findViewById(R.id.dodaj);
        promjeni = findViewById(R.id.promjeni);
        obrisi = findViewById(R.id.obrisi);

        Intent i = getIntent();
        boolean novaOsoba = i.getBooleanExtra("ontologija",false);
        if(!novaOsoba){
            ontologija = (Ontologija) i.getSerializableExtra("ontologija");
            naslov.setText(ontologija.getNaslov());
            autor.setText(ontologija.getAutor());
            duzina.setText(ontologija.getDuzina());
            tip.setText(ontologija.getTip());

            dodaj.setVisibility(View.INVISIBLE);
        }else {
            promjeni.setVisibility(View.INVISIBLE);
            obrisi.setVisibility(View.INVISIBLE);
        }


        nazad.setOnClickListener(new View.OnClickListener(){
            @Override
            public void onClick(View view) {
                nazad(true);
            }
        });

        dodaj.setOnClickListener(new View.OnClickListener(){
            @Override
            public void onClick(View view) {
                dodaj();
            }
        });

        promjeni.setOnClickListener(new View.OnClickListener(){
            @Override
            public void onClick(View view) {
                promjeni();
            }
        });

        obrisi.setOnClickListener(new View.OnClickListener(){
            @Override
            public void onClick(View view) {
                obrisi();
            }
        });

        restTask = new RESTTask();

         gson = new GsonBuilder().create();


    }

    private void obrisi() {
        restTask.execute(getString(R.string.REST_URL) + "/" + ontologija.getSifra(),"DELETE","");
    }

    private void promjeni() {
        ontologija.setNaslov(naslov.getText().toString());
        ontologija.setAutor(autor.getText().toString());
        ontologija.setDuzina(autor.getText().toString());
        ontologija.setTip(autor.getText().toString());

        restTask.execute(getString(R.string.REST_URL)+ "/" + ontologija.getSifra(),"PUT",gson.toJson(ontologija));
    }

    private void dodaj() {
        ontologija = new Ontologija();
        ontologija.setNaslov(naslov.getText().toString());
        ontologija.setAutor(autor.getText().toString());
        ontologija.setDuzina(autor.getText().toString());
        ontologija.setTip(autor.getText().toString());

        restTask.execute(getString(R.string.REST_URL) ,"POST",gson.toJson(ontologija));
    }

    void nazad(boolean ok){
        setResult(ok ? MainActivity.RESULT_OK : MainActivity.GRESKA, null);
        finish();
    }


    private class RESTTask extends AsyncTask<String, Void, Boolean> {
        protected Boolean doInBackground(String... parametri) {
            String stringUrl = parametri[0];
            String metoda=parametri[1];
            String json=parametri[2];
            Log.d("json", json);
            try {
                URL myUrl = new URL(stringUrl);
                HttpURLConnection connection =(HttpURLConnection)
                        myUrl.openConnection();
                connection.setDoOutput(true);
                connection.setDoInput(true);
                connection.setInstanceFollowRedirects(false);
                connection.setRequestMethod(metoda);
                connection.setRequestProperty("Content-Type","application/json");
                connection.setRequestProperty("charset", "utf-8");
                connection.setUseCaches (false);
                DataOutputStream wr = new DataOutputStream(connection.getOutputStream ());
                BufferedWriter writer = new BufferedWriter(new OutputStreamWriter(wr, "UTF-8"));
                writer.write(json);
                writer.close();
                wr.close();

                InputStream is = connection.getInputStream();

                is = new BufferedInputStream(connection.getInputStream());
                BufferedReader br = new BufferedReader(new InputStreamReader(is,"UTF-8"));
                String inputLine = "";
                while ((inputLine = br.readLine()) != null) {
                    Log.d("--->",inputLine);
                }
                br.close();
                is.close();



               return metoda.equals("POST") && connection.getResponseCode()==201 ? true : connection.getResponseCode()==200 ? true : false;




            }catch (Exception e){
                e.printStackTrace();
                return false;
            }
        }

        protected void onProgressUpdate(Integer... progress) {

        }

        protected void onPostExecute(Boolean ok) {

                nazad(ok);

        }
    }
}
