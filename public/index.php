<?php 
require_once('../private/initialize.php');
$page_title =  $page_title_default;
set_maintenance();

$last_record = last_record();
$prev_day = prev_day();
?>

<!doctype html>
<html lang="en">

<!-- html header -->
<?php include(SHARED_PATH . '/head.php') ?>

<body id="index-page">
    <!-- Preloader -->
    <script>
    </script>
    <div id="preloader">
        <div id="status" class="spinner-border text-secondary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <!-- navigation -->
    <?php include(SHARED_PATH . '/nav.php') ?>

    <main class="boxed">
        <?php show_admin_panel(); ?>

        <!-- Last updated -->
        <span class="d-none" id="phpdatetime"><?= fdate_index(); ?></span>
        <p class="bg-grey mt-4 shadowed bordered bordered-all p-3 px-3">
            <span class="badge badge-warning">Last updated:</span><br>
            <span id="datetime"></span>
        </p>

        <!-- Country -->
        <ul class="list-group mt-4 shadowed bordered bordered-all">
            <li class="list-group-item list-group-header bg-grey bordered bordered-top p-4">
                <aside>
                    <span class="text-warning">Confirmed</span><br>
                    <span
                        class="case-count display-3"><?= number_format($last_record['ng_confirmed']) ?><?= show_diff(country_confirmed_diff()) ?></span>
                </aside>
                <aside>
                    <div class="row no-gutters">
                        <div class="col-4">
                            <span class="text-danger">Deaths</span><br>
                            <span class="case-count h4"><?= number_format($last_record['ng_deaths']) ?>
                                <?= show_diff(country_deaths_diff(),'danger') ?></span>
                        </div>
                        <div class="col-4">
                            <span class="text-success">Discharged</span><br>
                            <span class="case-count h4"><?= number_format($last_record['ng_recovered']) ?>
                                <?= show_diff(country_recovered_diff(),'success') ?></span>
                        </div>
                        <div class="col-4">
                            <span class="text-secondary">Active</span><br>
                            <span class="case-count h4"><?= number_format($last_record['ng_active']) ?>
                                <?= show_diff(country_active_diff(),'secondary') ?></span>
                        </div>
                    </div>
                </aside>
            </li> <!-- end list-group-header -->

            <!-- Nigeria GeoChart -->
            <li class="list-group-item bordered">
                <div class="chart-info text-muted bg-grey bordered bordered-all mt-2">
                    <?php include(IMG_PATH . '/info.svg') ?>
                    Tap or hover on a state for details
                </div>
                <div class="chart" id="nigeria-geo"></div>
            </li>
            <!-- end Nigeria GeoChart -->

            <!-- Collapse -->
            <li class="list-group-item bg-grey bordered bordered-bottom">
                <a class="btn btn-dark bordered bordered-all my-3" data-toggle="collapse" href="#statesCollapse"
                    role="button" aria-expanded="false" aria-controls="statesCollapse">
                    Breakdown by states
                </a>

                <ul class="list-group list-group-flush collapse" id="statesCollapse">
                    <!-- States -->
                    <?php
                    $regions = disp_region_if_cases();
                    foreach ($regions as $value) : ?>
                    <li class="list-group-item bg-grey px-2">
                        <span class="float-left"><?= $value[0] ?></span>
                        <span class="case-count align-right"><?= number_format($value[1]) ?>
                            <?= show_diff($value[1] - $value[2]) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <!-- end Collapse -->

        </ul> <!-- end Country -->

        <!-- Statistics -->
        <ul class="list-group statistics spaced shadowed bordered bordered-all">
            <li class="list-group-item bordered bordered-top p-4">
                <h4>Nigeria Statistics</h4>
                <div class="chart" id="nigeria-stats"></div>
                <div class="text-center">
                    <span class="badge badge-warning">Confirmed</span>
                    <span class="badge badge-success">Discharged</span>
                    <span class="badge badge-danger">Deaths</span>
                </div>
                <div class="chart-info text-muted bg-grey bordered bordered-all mt-2">
                    <?php include(IMG_PATH . '/info.svg') ?>
                    Tap or hover on a marker for details
                </div>
            </li>

            <li class="list-group-item bordered bordered-bottom bg-grey">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-grey px-2">
                        <span class="">No. of states</span>
                        <span class="nudge-left align-right"><?= no_of_states() ?></span>
                    </li>
                    <li class="list-group-item bg-grey px-2">
                        <span class="">% Deaths</span>
                        <span class="align-right"><?= percent_deaths() ?></span>
                    </li>
                    <li class="list-group-item bg-grey px-2">
                        <span class="">% Discharged</span>
                        <span class="align-right"><?= percent_recovered() ?></span>
                    </li>
                    <li class="list-group-item bg-grey px-2">
                        <span class="">% Active</span>
                        <span class="align-right"><?= percent_active() ?></span>
                    </li>
                </ul>
            </li>
        </ul> <!-- end statistics -->

        <!-- Global -->
        <?php $world_stats = world_stats(); ?>
        <ul class="list-group spaced global-cases shadowed bordered bordered-all">
            <li class="list-group-item bordered bordered-top p-4">
                <h4 class="mb-4 py-1">Global Cases</h4>
                <div class="chart" id="world-geo"></div>
                <div class="chart-info text-muted bg-grey bordered bordered-all mt-2">
                    <?php include(IMG_PATH . '/info.svg') ?>
                    Tap or hover on a country for details <br>&nbsp;&nbsp;&nbsp; Colour-coded by 'Confirmed cases per
                    million'
                </div>
            </li>
            <!-- Collapse -->
            <li class="list-group-item bordered bordered-bottom bg-grey">

                <a class="btn btn-dark bordered bordered-all my-3" data-toggle="collapse" href="#globalCollapse"
                    role="button" aria-expanded="false" aria-controls="globalCollapse">
                    View global statistics
                </a>

                <ul class="list-group list-group-flush collapse" id="globalCollapse">
                    <li class="list-group-item bg-grey px-2">
                        <span class="float-left">Confirmed</span>
                        <span id="world-cases"
                            class="case-count align-right nudge-left"><?= number_format($world_stats['cases']) ?>
                            <?= show_diff($world_stats['todayCases']) ?></span>
                    </li>
                    <li class="list-group-item bg-grey px-2">
                        <span class="float-left">Deaths</span>
                        <span class="case-count align-right nudge-left"><?= number_format($world_stats['deaths']) ?>
                            <?= show_diff($world_stats['todayDeaths'], 'danger') ?></span>
                    </li>
                    <li class="list-group-item bg-grey px-2">
                        <span class="float-left">Discharged</span>
                        <span class="case-count align-right"><?= number_format($world_stats['recovered']) ?></span>
                    </li>
                    <li class="list-group-item bg-grey px-2">
                        <span class="float-left">Active</span>
                        <span class="case-count align-right"><?= number_format($world_stats['active']) ?></span>
                    </li>
                    <li class="list-group-item bg-grey px-2">
                        <span class="float-left">Critical</span>
                        <span class="case-count align-right"><?= number_format($world_stats['critical']) ?></span>
                    </li>
                    <li class="list-group-item bg-grey px-2">
                        <span class="float-left">Countries</span>
                        <span class="case-count align-right"><?= $world_stats['affectedCountries'] ?></span>
                    </li>
                    <li class="list-group-item bg-grey px-2">
                        <span class="float-left">% Deaths</span>
                        <span
                            class="case-count align-right"><?= round($world_stats['deaths']/$world_stats['cases']*100,1) ?>%</span>
                    </li>
                    <li class="list-group-item bg-grey px-2">
                        <span class="float-left">% Discharged</span>
                        <span
                            class="case-count align-right"><?= round($world_stats['recovered']/$world_stats['cases']*100,1) ?>%</span>
                    </li>
                    <li class="list-group-item bg-grey px-2">
                        <span class="float-left">% Active</span>
                        <span
                            class="case-count align-right"><?= round($world_stats['active']/$world_stats['cases']*100,1) ?>%</span>
                    </li>
                    <li class="list-group-item bg-grey px-2">
                        <span class="float-left">% Critical</span>
                        <span
                            class="case-count align-right"><?= round($world_stats['critical']/$world_stats['cases']*100,1) ?>%</span>
                    </li>
                </ul>
            </li><!-- end Collapse -->
        </ul> <!-- end Global-->

        <div class="legal spaced">
            <h4>Notice</h4>
            <hr>
            <ul class="list-unstyled">
                <li>This is a data repository for cases on the 2019 Novel Coronavirus (COVID-19) in Nigeria. It is
                    developed
                    and maintained by <a href="<?= STARBOX ?>">StarBox Technologies Ltd.</a> StarBox Technologies
                    provides
                    this website free for use as part of its commitment to help the Nigerian community in these
                    unprecedented times.</li>
                <li>This website is <b>not</b> an official government repository nor is it sponsored by any government.
                </li>
                <li>This website is for informational purposes only, please contact the appropriate public health
                    agencies
                    for enquiries on the pandemic, personal health, testing and isolation.</li>
                <li>
                    <b>Data Sources:</b>
                    <ol class="inner-list">
                        <li>Nigeria Centre for Disease Control (NCDC) - <a
                                href="https://covid19.ncdc.gov.ng/">https://covid19.ncdc.gov.ng/</a>
                        </li>
                        <li>2019 Novel Coronavirus COVID-19 (2019-nCoV) Data Repository by Johns Hopkins CSSE -
                            <a
                                href="https://github.com/CSSEGISandData/COVID-19">https://github.com/CSSEGISandData/COVID-19</a>
                        </li>
                        <li>Novel COVID-19 API - <a
                                href="https://github.com/NOVELCOVID/API">https://github.com/NOVELCOVID/API</a>
                        </li>
                    </ol>
                </li>
                <li>For enquiries please contact us at hello@starboxtech.com.</li>
                <li><b>Terms of Use:</b><br>This website and its contents herein, including all data, mapping, and
                    analysis,
                    copyright <?= date('Y') ?> StarBox Technologies Ltd, all rights reserved, is provided to the public
                    strictly for
                    informational, educational and academic research purposes. The Website relies upon publicly
                    available
                    data from multiple sources, that do not always agree. StarBox Technologies Ltd hereby disclaims any
                    and
                    all representations and warranties with respect to the Website, including accuracy, fitness for use,
                    and
                    merchantability. Reliance on the Website for medical guidance or use of the Website in commerce is
                    strictly prohibited.</li>
            </ul>
        </div> <!-- end legal -->

        <?php show_admin_panel() ?>
    </main>

    <!-- Footer -->
    <?php include(SHARED_PATH . '/footer.php') ?>

    <!-- Page scripts -->
    <?php include(SHARED_PATH . '/page_scripts.php') ?>
    <!-- Chart scripts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="<?= DIST_ROOT . '/data/charts.js?' . time() ?>"></script>
</body>

</html>